<?php

namespace App\Services;

use App\Models\FinanceProposal;
use App\Models\SchoolClass;
use App\Models\SchoolFeeType;
use App\Models\StudentBill;
use App\Models\StudentClassHistory;
use App\Models\PointTransaction;
use App\Models\StudentPointSummary;
use App\Models\StudentPayment;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FinanceService
{
    public function publishProposal(FinanceProposal $proposal, User $reviewer, ?string $notes = null): int
    {
        return DB::transaction(function () use ($proposal, $reviewer, $notes) {
            $proposal = FinanceProposal::query()->lockForUpdate()->findOrFail($proposal->id);

            if ($proposal->status !== 'pending') {
                throw ValidationException::withMessages(['proposal' => 'Usulan ini sudah ditinjau sebelumnya.']);
            }

            $students = $this->studentsInClass($proposal->school_class_id);

            if ($students->isEmpty()) {
                throw ValidationException::withMessages(['proposal' => 'Kelas belum memiliki siswa aktif.']);
            }

            if ($proposal->billing_mode === 'collective' && $proposal->amount < $students->count()) {
                throw ValidationException::withMessages(['amount' => 'Target kolektif terlalu kecil untuk dibagi ke seluruh siswa.']);
            }

            foreach ($this->proposalAmounts($proposal, $students) as $studentId => $amount) {
                StudentBill::query()->create([
                    'student_user_id' => $studentId,
                    'school_class_id' => $proposal->school_class_id,
                    'academic_year_id' => $proposal->academic_year_id,
                    'finance_proposal_id' => $proposal->id,
                    'created_by_user_id' => $reviewer->id,
                    'title' => $proposal->title,
                    'amount' => $amount,
                    'due_date' => $proposal->due_date,
                ]);
            }

            $proposal->update([
                'reviewed_by_user_id' => $reviewer->id,
                'review_notes' => $notes,
                'status' => 'published',
                'reviewed_at' => now(),
                'published_at' => now(),
            ]);

            return $students->count();
        });
    }

    public function rejectProposal(FinanceProposal $proposal, User $reviewer, ?string $notes = null): void
    {
        $updated = FinanceProposal::query()
            ->whereKey($proposal->id)
            ->where('status', 'pending')
            ->update([
                'reviewed_by_user_id' => $reviewer->id,
                'review_notes' => $notes,
                'status' => 'rejected',
                'reviewed_at' => now(),
            ]);

        if (! $updated) {
            throw ValidationException::withMessages(['proposal' => 'Usulan ini sudah ditinjau sebelumnya.']);
        }
    }

    public function issueStandardBills(
        SchoolFeeType $feeType,
        SchoolClass $schoolClass,
        string $period,
        int $amount,
        ?string $dueDate,
        User $actor,
    ): int {
        return DB::transaction(function () use ($feeType, $schoolClass, $period, $amount, $dueDate, $actor) {
            $created = 0;

            foreach ($this->studentsInClass($schoolClass->id) as $student) {
                $bill = StudentBill::query()->firstOrCreate([
                    'student_user_id' => $student->id,
                    'school_fee_type_id' => $feeType->id,
                    'billing_period' => $period,
                ], [
                    'school_class_id' => $schoolClass->id,
                    'academic_year_id' => $schoolClass->academic_year_id,
                    'created_by_user_id' => $actor->id,
                    'title' => $feeType->name.' - '.$period,
                    'amount' => $amount,
                    'due_date' => $dueDate,
                ]);

                $created += $bill->wasRecentlyCreated ? 1 : 0;
            }

            return $created;
        });
    }

    public function recordPayment(StudentBill $bill, int $amount, string $paidOn, string $method, User $receiver, array $extra = []): StudentPayment
    {
        return DB::transaction(function () use ($bill, $amount, $paidOn, $method, $receiver, $extra) {
            $bill = StudentBill::query()->lockForUpdate()->findOrFail($bill->id);
            $outstanding = $bill->amount - $bill->paid_amount;

            if ($amount > $outstanding) {
                throw ValidationException::withMessages([
                    'amount' => 'Pembayaran melebihi sisa tagihan Rp '.number_format($outstanding, 0, ',', '.').'.',
                ]);
            }

            $payment = StudentPayment::query()->create([
                'student_bill_id' => $bill->id,
                'received_by_user_id' => $receiver->id,
                'amount' => $amount,
                'paid_on' => $paidOn,
                'payment_method' => $method,
                'reference' => $extra['reference'] ?? null,
                'proof_path' => $extra['proof_path'] ?? null,
                'notes' => $extra['notes'] ?? null,
                'status' => 'verified',
                'reviewed_by_user_id' => $receiver->id,
                'reviewed_at' => now(),
            ]);

            $paidAmount = $bill->paid_amount + $amount;
            $bill->update([
                'paid_amount' => $paidAmount,
                'status' => $paidAmount >= $bill->amount ? 'paid' : 'partial',
            ]);

            return $payment;
        });
    }

    public function submitPaymentConfirmation(StudentBill $bill, User $submitter, array $data): StudentPayment
    {
        return DB::transaction(function () use ($bill, $submitter, $data) {
            $bill = StudentBill::query()->lockForUpdate()->findOrFail($bill->id);

            if ($bill->status === 'paid') {
                throw ValidationException::withMessages(['bill' => 'Tagihan ini sudah lunas.']);
            }

            if ($bill->payments()->where('status', 'pending')->exists()) {
                throw ValidationException::withMessages(['bill' => 'Masih ada konfirmasi pembayaran yang menunggu verifikasi.']);
            }

            if ((int) $data['amount'] > $bill->outstanding_amount) {
                throw ValidationException::withMessages(['amount' => 'Nominal melebihi sisa tagihan.']);
            }

            return StudentPayment::query()->create([
                'student_bill_id' => $bill->id,
                'submitted_by_user_id' => $submitter->id,
                'received_by_user_id' => $submitter->id,
                'amount' => $data['amount'],
                'status' => 'pending',
                'paid_on' => $data['paid_on'],
                'payment_method' => $data['payment_method'],
                'reference' => $data['reference'] ?? null,
                'proof_path' => $data['proof_path'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    public function verifyPayment(StudentPayment $payment, User $reviewer): void
    {
        DB::transaction(function () use ($payment, $reviewer) {
            $payment = StudentPayment::query()->lockForUpdate()->findOrFail($payment->id);
            $bill = StudentBill::query()->lockForUpdate()->findOrFail($payment->student_bill_id);

            if ($payment->status !== 'pending') {
                throw ValidationException::withMessages(['payment' => 'Konfirmasi pembayaran ini sudah ditinjau.']);
            }

            if ($payment->amount > $bill->outstanding_amount) {
                throw ValidationException::withMessages(['payment' => 'Nominal konfirmasi melebihi sisa tagihan saat ini.']);
            }

            $paidAmount = $bill->paid_amount + $payment->amount;
            $bill->update([
                'paid_amount' => $paidAmount,
                'status' => $paidAmount >= $bill->amount ? 'paid' : 'partial',
            ]);
            $payment->update([
                'status' => 'verified',
                'received_by_user_id' => $reviewer->id,
                'reviewed_by_user_id' => $reviewer->id,
                'reviewed_at' => now(),
            ]);
        });
    }

    public function rejectPayment(StudentPayment $payment, User $reviewer, string $notes): void
    {
        $updated = StudentPayment::query()
            ->whereKey($payment->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'reviewed_by_user_id' => $reviewer->id,
                'review_notes' => $notes,
                'reviewed_at' => now(),
            ]);

        if (! $updated) {
            throw ValidationException::withMessages(['payment' => 'Konfirmasi pembayaran ini sudah ditinjau.']);
        }
    }

    public function promoteStudent(User $student, SchoolClass $newClass): void
    {
        DB::transaction(function () use ($student, $newClass) {
            $profile = $student->studentProfile()->lockForUpdate()->firstOrFail();
            $oldClassId = $profile->school_class_id;

            if ($oldClassId === $newClass->id) {
                throw ValidationException::withMessages(['school_class_id' => 'Siswa sudah berada di kelas tersebut.']);
            }

            if ($oldClassId) {
                $history = StudentClassHistory::query()
                    ->where('student_user_id', $student->id)
                    ->whereNull('ended_on')
                    ->latest('id')
                    ->first();

                if ($history) {
                    $history->update(['ended_on' => today()]);
                } else {
                    $oldClass = SchoolClass::query()->find($oldClassId);
                    StudentClassHistory::query()->create([
                        'student_user_id' => $student->id,
                        'school_class_id' => $oldClassId,
                        'academic_year_id' => $oldClass?->academic_year_id,
                        'started_on' => $oldClass?->academicYear?->starts_on ?? $student->created_at?->toDateString() ?? today(),
                        'ended_on' => today(),
                    ]);
                }
            }

            $profile->update(['school_class_id' => $newClass->id]);
            PointTransaction::query()->create([
                'student_user_id' => $student->id,
                'type' => 'reset',
                'points' => 0,
                'description' => 'Reset poin saat naik ke '.$newClass->name,
            ]);
            StudentPointSummary::query()->updateOrCreate(
                ['student_user_id' => $student->id],
                ['general_points' => 0, 'attitude_points' => 0, 'attendance_points' => 0, 'achievement_points' => 0, 'label' => 'Perlu Dipantau']
            );
            StudentClassHistory::query()->create([
                'student_user_id' => $student->id,
                'school_class_id' => $newClass->id,
                'academic_year_id' => $newClass->academic_year_id,
                'started_on' => today(),
            ]);

            StudentBill::query()
                ->where('student_user_id', $student->id)
                ->where('status', '!=', 'paid')
                ->update(['is_arrears' => true]);
        });
    }

    private function studentsInClass(int $schoolClassId): Collection
    {
        return User::query()
            ->where('role', 'student')
            ->where('is_active', true)
            ->whereHas('studentProfile', fn ($query) => $query->where('school_class_id', $schoolClassId))
            ->orderBy('id')
            ->get();
    }

    private function proposalAmounts(FinanceProposal $proposal, Collection $students): array
    {
        if ($proposal->billing_mode === 'per_student') {
            return $students->mapWithKeys(fn (User $student) => [$student->id => $proposal->amount])->all();
        }

        $base = intdiv($proposal->amount, $students->count());
        $remainder = $proposal->amount % $students->count();

        return $students->values()->mapWithKeys(
            fn (User $student, int $index) => [$student->id => $base + ($index < $remainder ? 1 : 0)]
        )->all();
    }
}
