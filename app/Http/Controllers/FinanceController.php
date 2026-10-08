<?php

namespace App\Http\Controllers;

use App\Models\FinanceProposal;
use App\Models\SchoolClass;
use App\Models\SchoolFeeType;
use App\Models\StudentBill;
use App\Models\StudentPayment;
use App\Models\User;
use App\Services\FinanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FinanceController extends Controller
{
    public function index(Request $request, string $section = 'bills')
    {
        $user = $request->user();
        $canManage = $user->canDo('finance.manage');
        $isPayer = $user->hasAnyRole(['student', 'parent']);
        $isProposer = ! $canManage && SchoolClass::query()->where('homeroom_teacher_id', $user->id)->exists();
        abort_unless($canManage || $isProposer || $isPayer, 403);
        $allowedSections = $canManage
            ? ['bills', 'issue', 'fee-types', 'proposals', 'record-payment', 'confirmations', 'payments']
            : ($isPayer
                ? ['bills', 'payments']
                : ['bills', 'proposals']);
        abort_unless(in_array($section, $allowedSections, true), 403);

        $studentIds = $user->hasRole('student')
            ? collect([$user->id])
            : ($user->hasRole('parent') ? $user->children()->pluck('users.id') : collect());

        $bills = StudentBill::query()->with(['student.studentProfile.schoolClass', 'schoolClass', 'payments', 'proposal.proposer'])
            ->when($user->hasRole('parent'), fn ($query) => $query->whereIn('student_user_id', $studentIds))
            ->when($user->hasRole('student'), fn ($query) => $query->whereIn('student_user_id', $studentIds)
                ->whereHas('proposal', fn ($proposal) => $proposal->where('billing_mode', 'collective')))
            ->when(! $canManage && ! $isPayer && $isProposer, fn ($query) => $query->whereHas('proposal', fn ($proposal) => $proposal
                ->where('billing_mode', 'collective')
                ->whereHas('schoolClass', fn ($class) => $class->where('homeroom_teacher_id', $user->id))))
            ->when(! $canManage && ! $isPayer && ! $isProposer, fn ($query) => $query->whereRaw('1 = 0'))
            ->latest()
            ->paginate(40)
            ->withQueryString();

        $payments = StudentPayment::query()
            ->with(['bill.student', 'submittedBy', 'reviewer'])
            ->when($section === 'confirmations', fn ($query) => $query->where('status', 'pending'))
            ->when($canManage && $section === 'confirmations', fn ($query) => $query->whereHas('bill', fn ($bill) => $bill
                ->whereDoesntHave('proposal', fn ($proposal) => $proposal->where('billing_mode', 'collective'))))
            ->when($user->hasAnyRole(['student', 'parent']), fn ($query) => $query->whereHas('bill', fn ($bill) => $bill->whereIn('student_user_id', $studentIds)
                ->when($user->hasRole('student'), fn ($bills) => $bills->whereHas('proposal', fn ($proposal) => $proposal->where('billing_mode', 'collective')))))
            ->when(! $canManage && ! $isPayer && $isProposer, fn ($query) => $query->whereHas('bill.proposal', fn ($proposal) => $proposal
                ->where('billing_mode', 'collective')->whereHas('schoolClass', fn ($class) => $class->where('homeroom_teacher_id', $user->id))))
            ->when(! $canManage && ! $isPayer && ! $isProposer, fn ($query) => $query->whereRaw('1 = 0'))
            ->latest()
            ->paginate(30, ['*'], 'payments_page')
            ->withQueryString();

        $proposals = FinanceProposal::query()
            ->with([
                'schoolClass',
                'proposer',
                'bills.student.studentProfile',
                'bills.payments' => fn ($query) => $query->latest(),
            ])
            ->when(! $canManage && $isProposer, fn ($query) => $query->whereHas('schoolClass', fn ($class) => $class->where('homeroom_teacher_id', $user->id)))
            ->when(! $canManage && ! $isProposer, fn ($query) => $query->whereRaw('1 = 0'))
            ->latest()
            ->get();

        $classes = $canManage
            ? SchoolClass::query()->with('academicYear')->orderBy('name')->get()
            : SchoolClass::query()->where('homeroom_teacher_id', $user->id)
                ->with('academicYear')->orderBy('name')->get();
        $requestedClassId = $request->integer('school_class_id');
        abort_if($requestedClassId && ! $classes->contains('id', $requestedClassId), 404);
        $selectedClassId = $requestedClassId ?: $classes->first()?->id;
        $students = $canManage && $selectedClassId
            ? User::query()->where('role', 'student')->where('is_active', true)
                ->whereHas('studentProfile', fn ($profile) => $profile->where('school_class_id', $selectedClassId))
                ->with('studentProfile')->orderBy('name')->get()
            : collect();

        $availableBills = $canManage
            ? StudentBill::query()->with('student')->where('status', '!=', 'paid')
                ->whereDoesntHave('proposal', fn ($proposal) => $proposal->where('billing_mode', 'collective'))
                ->orderByDesc('created_at')->get()
            : ($isProposer
                ? StudentBill::query()->with('student')->whereHas('proposal', fn ($q) => $q->where('billing_mode', 'collective')->whereHas('schoolClass', fn ($class) => $class->where('homeroom_teacher_id', $user->id)))->where('status', '!=', 'paid')->orderByDesc('created_at')->get()
                : collect());

        return view('finance.index', [
            'section' => $section,
            'user' => $user,
            'canManage' => $canManage,
            'isPayer' => $isPayer,
            'isProposer' => $isProposer,
            'isStudent' => $user->hasRole('student'),
            'bills' => $bills,
            'payments' => $payments,
            'proposals' => $proposals,
            'feeTypes' => $canManage ? SchoolFeeType::query()->where('is_active', true)->orderBy('name')->get() : collect(),
            'classes' => $classes,
            'selectedClassId' => $selectedClassId,
            'students' => $students,
            'availableBills' => $availableBills,
        ]);
    }

    public function storeFeeType(Request $request)
    {
        $request->merge(['code' => Str::upper((string) $request->input('code'))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50', 'unique:school_fee_types,code'],
            'billing_cycle' => ['required', 'in:monthly,annual,once'],
            'default_amount' => ['required', 'integer', 'min:1'],
        ]);

        SchoolFeeType::query()->create($data);

        return back()->with('status', 'Jenis tagihan berhasil dibuat.');
    }

    public function issueBills(Request $request, FinanceService $finance)
    {
        $data = $request->validate([
            'school_fee_type_id' => ['required', 'exists:school_fee_types,id'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'billing_period' => ['required', 'string', 'max:30'],
            'amounts' => ['required', 'array', 'min:1'],
            'amounts.*' => ['nullable', 'integer', 'min:1'],
            'due_date' => ['nullable', 'date'],
        ]);

        if (collect($data['amounts'])->filter(fn ($amount) => $amount !== null && $amount !== '')->isEmpty()) {
            throw ValidationException::withMessages(['amounts' => 'Isi nominal untuk minimal satu siswa.']);
        }

        $feeType = SchoolFeeType::query()->findOrFail($data['school_fee_type_id']);

        if (($feeType->billing_cycle === 'monthly' && ! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $data['billing_period']))
            || ($feeType->billing_cycle === 'annual' && ! preg_match('/^\d{4}\/\d{4}$/', $data['billing_period']))) {
            throw ValidationException::withMessages([
                'billing_period' => $feeType->billing_cycle === 'monthly'
                    ? 'Periode bulanan harus berformat YYYY-MM.'
                    : 'Periode tahunan harus berformat YYYY/YYYY.',
            ]);
        }

        $count = $finance->issueStandardBills(
            $feeType,
            SchoolClass::query()->findOrFail($data['school_class_id']),
            $data['billing_period'],
            $data['amounts'],
            $data['due_date'] ?? null,
            $request->user(),
        );

        return back()->with('status', "{$count} tagihan baru berhasil diterbitkan. Tagihan duplikat dilewati.");
    }

    public function storeProposal(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'billing_mode' => ['required', 'in:per_student,collective'],
            'amount' => ['required', 'integer', 'min:1'],
            'due_date' => ['nullable', 'date'],
        ]);

        $classQuery = SchoolClass::query();
        if (! $user->canDo('finance.manage')) {
            $classQuery->where(function ($query) use ($user) {
                $query->where('homeroom_teacher_id', $user->id)
                    ->orWhere('class_leader_user_id', $user->id);
            });
        }

        $schoolClass = $classQuery->findOrFail($data['school_class_id']);

        if ($data['billing_mode'] === 'collective' && $schoolClass->homeroom_teacher_id !== $user->id) {
            abort(403);
        }
        if ($data['billing_mode'] === 'collective') {
            $studentCount = User::query()->where('role', 'student')->where('is_active', true)
                ->whereHas('studentProfile', fn ($profile) => $profile->where('school_class_id', $schoolClass->id))->count();
            if ($studentCount === 0 || (int) $data['amount'] < $studentCount) {
                throw ValidationException::withMessages(['amount' => $studentCount === 0
                    ? 'Kelas belum memiliki siswa aktif.'
                    : 'Target kolektif tidak cukup untuk dibagi ke seluruh siswa kelas.']);
            }
        }

        $proposal = FinanceProposal::query()->create([
            ...$data,
            'proposed_by_user_id' => $user->id,
            'academic_year_id' => $schoolClass->academic_year_id,
        ]);

        if ($data['billing_mode'] === 'collective') {
            app(FinanceService::class)->publishProposal($proposal, null);
        }

        return back()->with('status', $data['billing_mode'] === 'collective'
            ? 'Tagihan kolektif langsung diterbitkan untuk siswa kelas Anda.'
            : 'Usulan biaya dikirim dan menunggu verifikasi TU.');
    }

    public function approveProposal(Request $request, FinanceProposal $proposal, FinanceService $finance)
    {
        $data = $request->validate(['review_notes' => ['nullable', 'string', 'max:2000']]);
        $count = $finance->publishProposal($proposal, $request->user(), $data['review_notes'] ?? null);

        return back()->with('status', "Usulan disetujui dan {$count} tagihan siswa diterbitkan.");
    }

    public function rejectProposal(Request $request, FinanceProposal $proposal, FinanceService $finance)
    {
        $data = $request->validate(['review_notes' => ['required', 'string', 'max:2000']]);
        $finance->rejectProposal($proposal, $request->user(), $data['review_notes']);

        return back()->with('status', 'Usulan ditolak dan tidak ada tagihan yang diterbitkan.');
    }

    public function storePayment(Request $request, StudentBill $bill, FinanceService $finance)
    {
        $bill->loadMissing('proposal');
        abort_unless($this->canCollectBill($request->user(), $bill), 403);

        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:'.$bill->outstanding_amount],
            'paid_on' => ['required', 'date', 'before_or_equal:today'],
            'payment_method' => ['required', 'in:cash,transfer,other'],
            'reference' => ['nullable', 'string', 'max:150'],
            'proof' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->hasFile('proof')) {
            $data['proof_path'] = $request->file('proof')->store('payment-proofs', 'local');
        }

        $finance->recordPayment(
            $bill,
            (int) $data['amount'],
            $data['paid_on'],
            $data['payment_method'],
            $request->user(),
            $data,
        );

        return back()->with('status', 'Pembayaran berhasil dicatat.');
    }

    public function submitPaymentConfirmation(Request $request, StudentBill $bill, FinanceService $finance)
    {
        abort_unless($this->canAccessBill($request->user(), $bill), 403);
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:'.$bill->outstanding_amount],
            'paid_on' => ['required', 'date', 'before_or_equal:today'],
            'payment_method' => ['required', 'in:cash,transfer,other'],
            'reference' => ['nullable', 'string', 'max:150'],
            'proof' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->hasFile('proof')) {
            $data['proof_path'] = $request->file('proof')->store('payment-proofs', 'local');
        }

        $finance->submitPaymentConfirmation($bill, $request->user(), $data);

        return redirect()->route('finance.payment-history')->with('status', 'Konfirmasi pembayaran dikirim dan menunggu verifikasi tata usaha.');
    }

    public function approvePayment(Request $request, StudentPayment $payment, FinanceService $finance)
    {
        $payment->loadMissing('bill.proposal');
        abort_unless($this->canCollectBill($request->user(), $payment->bill), 403);

        $finance->verifyPayment($payment, $request->user());

        return back()->with('status', 'Pembayaran diverifikasi dan saldo tagihan diperbarui.');
    }

    public function rejectPayment(Request $request, StudentPayment $payment, FinanceService $finance)
    {
        $payment->loadMissing('bill.proposal');
        abort_unless($this->canCollectBill($request->user(), $payment->bill), 403);

        $data = $request->validate(['review_notes' => ['required', 'string', 'max:1000']]);
        $finance->rejectPayment($payment, $request->user(), $data['review_notes']);

        return back()->with('status', 'Konfirmasi pembayaran ditolak.');
    }

    public function paymentProof(Request $request, StudentPayment $payment)
    {
        $payment->loadMissing('bill.proposal');
        abort_unless(
            $payment->proof_path && (
                $request->user()->canDo('finance.manage')
                || $this->canAccessBill($request->user(), $payment->bill)
                || $this->canCollectBill($request->user(), $payment->bill)
            ),
            403
        );
        abort_unless(Storage::disk('local')->exists($payment->proof_path), 404);

        return Storage::disk('local')->response($payment->proof_path);
    }

    public function promoteStudent(Request $request, User $student, FinanceService $finance)
    {
        abort_unless($student->isRole('student'), 404);
        $data = $request->validate(['school_class_id' => ['required', 'exists:school_classes,id']]);
        $finance->promoteStudent($student, SchoolClass::query()->findOrFail($data['school_class_id']));

        return back()->with('status', 'Kelas siswa diperbarui. Tagihan lama yang belum lunas ditandai sebagai tunggakan.');
    }

    public function canCollectBill(User $user, StudentBill $bill): bool
    {
        if ($bill->finance_proposal_id) {
            $bill->loadMissing('proposal');

            if ($bill->proposal?->billing_mode === 'collective') {
                return $bill->proposal->proposed_by_user_id === $user->id
                    && $bill->schoolClass?->homeroom_teacher_id === $user->id;
            }
        }

        if ($user->canDo('finance.manage')) {
            return true;
        }

        if ($bill->finance_proposal_id) {
            return $bill->proposal && $bill->proposal->proposed_by_user_id === $user->id;
        }

        return false;
    }

    private function canAccessBill(User $user, StudentBill $bill): bool
    {
        if ($user->hasRole('student')) {
            $bill->loadMissing('proposal');

            return $bill->student_user_id === $user->id && $bill->proposal?->billing_mode === 'collective';
        }

        if ($user->hasRole('parent')) {
            return $user->children()->whereKey($bill->student_user_id)->exists();
        }

        return false;
    }
}
