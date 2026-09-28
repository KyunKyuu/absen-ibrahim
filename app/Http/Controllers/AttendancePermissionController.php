<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendancePermission;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\StudentClassHistory;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AttendancePermissionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isStudent = $user->isRole('student');
        $isParent = $user->isRole('parent');
        $isAdmin = $user->canDo('school.manage') || $user->canDo('attendance.manage');
        $isTeacher = $user->isRole('teacher');

        $children = $isParent
            ? $user->children()->with('studentProfile.schoolClass')->get()
            : collect();

        // Determine accessible students to filter & query
        $permissionsQuery = AttendancePermission::query()
            ->with(['student.studentProfile.schoolClass', 'submittedBy', 'reviewer', 'schoolClass']);

        if ($isStudent) {
            $permissionsQuery->where('student_user_id', $user->id);
            $studentsToApply = collect([$user]);
            $classes = collect();
        } elseif ($isParent) {
            $childIds = $children->pluck('id');
            $permissionsQuery->whereIn('student_user_id', $childIds);
            $studentsToApply = $children;
            $classes = SchoolClass::query()->whereIn('id', $children->pluck('studentProfile.school_class_id')->filter())->get();
        } elseif ($isTeacher && ! $isAdmin) {
            $homeroomClassIds = SchoolClass::query()->where('homeroom_teacher_id', $user->id)
                ->orWhereHas('teachingAssignments', fn ($q) => $q->where('teacher_user_id', $user->id))->pluck('id');
            $permissionsQuery->whereIn('school_class_id', $homeroomClassIds);
            $studentsToApply = User::query()
                ->where('role', 'student')
                ->whereHas('studentProfile', fn ($q) => $q->whereIn('school_class_id', $homeroomClassIds))
                ->orderBy('name')
                ->get();
            $classes = SchoolClass::query()->whereIn('id', $homeroomClassIds)->get();
        } else {
            // Superadmin, TU, or manager
            $studentsToApply = User::query()->where('role', 'student')->orderBy('name')->get();
            $classes = SchoolClass::query()->orderBy('name')->get();
        }

        // Filters
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:all,pending,approved,rejected,cancelled'],
            'type' => ['nullable', 'in:all,sick,excused,other'],
            'class_id' => ['nullable', 'integer'],
            'student_id' => ['nullable', 'integer'],
        ]);

        $statusFilter = $filters['status'] ?? 'all';
        $typeFilter = $filters['type'] ?? 'all';

        // Count queries for summary chips
        $baseCountQuery = clone $permissionsQuery;
        $counts = [
            'all' => (clone $baseCountQuery)->count(),
            'pending' => (clone $baseCountQuery)->where('status', 'pending')->count(),
            'approved' => (clone $baseCountQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $baseCountQuery)->where('status', 'rejected')->count(),
        ];

        // Apply filters to main listing
        $permissions = $permissionsQuery
            ->when($statusFilter !== 'all', fn ($q) => $q->where('status', $statusFilter))
            ->when($typeFilter !== 'all', fn ($q) => $q->where('type', $typeFilter))
            ->when($filters['class_id'] ?? null, fn ($q, $classId) => $q->where('school_class_id', $classId))
            ->when($filters['student_id'] ?? null, fn ($q, $studentId) => $q->where('student_user_id', $studentId))
            ->when($filters['q'] ?? null, fn ($q, $search) => $q->where(function ($sub) use ($search) {
                $sub->whereHas('student', fn ($s) => $s->where('name', 'like', "%{$search}%"))
                    ->orWhere('reason', 'like', "%{$search}%");
            }))
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'approved' THEN 1 ELSE 2 END")
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('attendance.permissions.index', [
            'user' => $user,
            'isStudent' => $isStudent,
            'isParent' => $isParent,
            'isAdmin' => $isAdmin,
            'isTeacher' => $isTeacher,
            'children' => $children,
            'studentsToApply' => $studentsToApply,
            'classes' => $classes,
            'permissions' => $permissions,
            'counts' => $counts,
            'filters' => $filters,
            'statusFilter' => $statusFilter,
            'typeFilter' => $typeFilter,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $isStudent = $user->isRole('student');
        $isParent = $user->isRole('parent');

        $rules = [
            'type' => ['required', 'in:sick,excused,other'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'min:3', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ];

        if (! $isStudent) {
            $rules['student_user_id'] = ['required', 'integer', 'exists:users,id'];
        }

        $validated = $request->validate($rules);

        // Resolve target student
        if ($isStudent) {
            $student = $user;
        } elseif ($isParent) {
            $student = $user->children()->findOrFail($validated['student_user_id']);
        } else {
            abort_unless($user->canDo('attendance.manage') || $user->isHomeroomTeacher(), 403);
            $student = User::query()->where('role', 'student')->findOrFail($validated['student_user_id']);
        }

        // Calculate student's active class, academic year, semester
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        $classHistory = StudentClassHistory::query()
            ->where('student_user_id', $student->id)
            ->whereDate('started_on', '<=', $startDate)
            ->where(fn ($query) => $query->whereNull('ended_on')->orWhereDate('ended_on', '>=', $startDate))
            ->latest('started_on')
            ->first();

        $schoolClassId = $classHistory?->school_class_id
            ?? $student->studentProfile()->value('school_class_id');
        $academicYearId = $classHistory?->academic_year_id
            ?? ($schoolClassId ? SchoolClass::query()->whereKey($schoolClassId)->value('academic_year_id') : null);
        $semesterId = Semester::query()
            ->whereDate('starts_on', '<=', $startDate)
            ->whereDate('ends_on', '>=', $startDate)
            ->value('id');

        // Handle attachment
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attendance-permissions', 'local');
        }

        $permission = AttendancePermission::create([
            'student_user_id' => $student->id,
            'submitted_by_user_id' => $user->id,
            'school_class_id' => $schoolClassId,
            'academic_year_id' => $academicYearId,
            'semester_id' => $semesterId,
            'type' => $validated['type'],
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'reason' => $validated['reason'],
            'attachment_path' => $attachmentPath,
            'status' => 'pending',
        ]);

        $message = "Pengajuan {$permission->typeLabel()} untuk {$student->name} berhasil dikirim! Menunggu konfirmasi pihak sekolah.";

        return back()->with('status', $message);
    }

    public function approve(Request $request, AttendancePermission $permission)
    {
        $user = $request->user();
        abort_unless($this->canReviewPermission($user, $permission), 403);

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($permission, $user, $validated) {
            $startDate = Carbon::parse($permission->start_date);
            $endDate = Carbon::parse($permission->end_date);
            $period = CarbonPeriod::create($startDate, $endDate);

            $statusToRecord = $permission->type === 'sick' ? 'sick' : 'excused';
            $reasonNote = ($permission->typeLabel()).': '.$permission->reason;

            foreach ($period as $date) {
                // Upsert attendance record for each day in range
                $attendance = Attendance::query()
                    ->where('student_user_id', $permission->student_user_id)
                    ->whereDate('attendance_date', $date->toDateString())
                    ->first();

                if ($attendance) {
                    $attendance->update([
                        'status' => $statusToRecord,
                        'source' => 'permit',
                        'notes' => $reasonNote,
                        'created_by_user_id' => $user->id,
                    ]);
                } else {
                    Attendance::query()->create([
                        'student_user_id' => $permission->student_user_id,
                        'school_class_id' => $permission->school_class_id,
                        'academic_year_id' => $permission->academic_year_id,
                        'semester_id' => $permission->semester_id,
                        'attendance_date' => $date->toDateString(),
                        'status' => $statusToRecord,
                        'source' => 'permit',
                        'is_ontime' => false,
                        'is_within_radius' => false,
                        'notes' => $reasonNote,
                        'created_by_user_id' => $user->id,
                    ]);
                }
            }

            $permission->update([
                'status' => 'approved',
                'reviewed_by_user_id' => $user->id,
                'reviewed_at' => now(),
                'review_notes' => $validated['notes'] ?? 'Disetujui oleh '.$user->name,
            ]);
        });

        return back()->with('status', "Pengajuan izin {$permission->student?->name} telah disetujui dan dicatat ke rekap absensi.");
    }

    public function reject(Request $request, AttendancePermission $permission)
    {
        $user = $request->user();
        abort_unless($this->canReviewPermission($user, $permission), 403);

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $permission->update([
            'status' => 'rejected',
            'reviewed_by_user_id' => $user->id,
            'reviewed_at' => now(),
            'review_notes' => $validated['notes'] ?? 'Ditolak',
        ]);

        return back()->with('status', "Pengajuan izin {$permission->student?->name} telah ditolak.");
    }

    public function cancel(Request $request, AttendancePermission $permission)
    {
        $user = $request->user();
        abort_unless(
            $permission->submitted_by_user_id === $user->id ||
            $permission->student_user_id === $user->id ||
            $user->canDo('attendance.manage'),
            403
        );

        if ($permission->status !== 'pending') {
            throw ValidationException::withMessages(['permission' => 'Pengajuan yang sudah diproses tidak dapat dibatalkan.']);
        }

        if ($permission->attachment_path && Storage::disk('local')->exists($permission->attachment_path)) {
            Storage::disk('local')->delete($permission->attachment_path);
        }

        $permission->delete();

        return back()->with('status', 'Pengajuan izin berhasil dibatalkan.');
    }

    public function attachment(Request $request, AttendancePermission $permission)
    {
        $user = $request->user();
        abort_unless($this->canViewAttachment($user, $permission), 403);

        abort_unless($permission->attachment_path && Storage::disk('local')->exists($permission->attachment_path), 404);

        return Storage::disk('local')->response($permission->attachment_path);
    }

    private function canReviewPermission(User $user, AttendancePermission $permission): bool
    {
        if ($user->canDo('school.manage') || $user->hasRole('superadmin')) {
            return true;
        }

        if ($permission->school_class_id) {
            return SchoolClass::query()
                ->where('id', $permission->school_class_id)
                ->where('homeroom_teacher_id', $user->id)
                ->exists();
        }

        return false;
    }

    private function canViewAttachment(User $user, AttendancePermission $permission): bool
    {
        if ($this->canReviewPermission($user, $permission)) {
            return true;
        }

        if ($permission->submitted_by_user_id === $user->id || $permission->student_user_id === $user->id) {
            return true;
        }

        if ($user->isRole('parent') && $user->children()->where('users.id', $permission->student_user_id)->exists()) {
            return true;
        }

        return false;
    }
}
