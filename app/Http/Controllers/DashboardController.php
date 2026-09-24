<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Semester;
use App\Models\StudentClassHistory;
use App\Models\StudentPointSummary;
use App\Models\User;
use App\Services\StudentProgressService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, StudentProgressService $progressService)
    {
        $user = $request->user();
        $students = collect();
        $leaderboard = collect();
        $stats = [];
        $classProgress = collect();
        $podium = collect();
        $podiumClasses = collect();
        $podiumClassId = null;

        if ($user->isRole('student')) {
            $user->loadMissing('pointSummary');
        }

        if (! in_array($user->role, ['student', 'parent'], true)) {
            $visibleClassIds = SchoolClass::query()
                ->when(! $user->canDo('school.manage'), fn ($query) => $query->where(function ($classes) use ($user) {
                    $classes->where('homeroom_teacher_id', $user->id)
                        ->orWhereHas('teachingAssignments', fn ($assignments) => $assignments->where('teacher_user_id', $user->id));
                }))
                ->pluck('id');
            $students = User::query()
                ->where('role', 'student')
                ->when(! $user->canDo('school.manage'), fn ($query) => $query->whereHas('studentProfile', fn ($profile) => $profile->whereIn('school_class_id', $visibleClassIds)))
                ->with(['studentProfile.schoolClass', 'pointSummary'])
                ->orderBy('name')
                ->get();

            $stats = [
                'students' => $students->count(),
                'teachers' => User::query()->where('role', 'teacher')->count(),
                'parents' => User::query()->where('role', 'parent')->count(),
                'todayAttendance' => Attendance::query()->whereDate('attendance_date', today())
                    ->when(! $user->canDo('school.manage'), fn ($query) => $query->whereIn('student_user_id', $students->pluck('id')))->count(),
                'priority' => StudentPointSummary::query()->where('label', 'Prioritas Perhatian Guru')
                    ->when(! $user->canDo('school.manage'), fn ($query) => $query->whereHas('student.studentProfile', fn ($profile) => $profile->whereIn('school_class_id', $visibleClassIds)))->count(),
                'totalPoints' => (int) StudentPointSummary::query()
                    ->when(! $user->canDo('school.manage'), fn ($query) => $query->whereHas('student.studentProfile', fn ($profile) => $profile->whereIn('school_class_id', $visibleClassIds)))
                    ->sum('general_points'),
            ];
            $stats['checkInRate'] = $stats['students'] > 0
                ? min(100, (int) round($stats['todayAttendance'] / $stats['students'] * 100))
                : 0;

            $classProgress = SchoolClass::query()
                ->whereIn('id', $visibleClassIds)
                ->with('students.user.pointSummary')
                ->withCount('students')
                ->get()
                ->map(function (SchoolClass $class) {
                    $scores = $class->students->map(fn ($profile) => (int) ($profile->user?->pointSummary?->general_points ?? 0));
                    $class->average_points = $scores->isNotEmpty() ? (int) round($scores->avg()) : 0;
                    $class->active_students = $class->students->count();

                    return $class;
                })
                ->sortByDesc('average_points')->values();

            $leaderboard = StudentPointSummary::query()
                ->with('student.studentProfile.schoolClass')
                ->when(! $user->canDo('school.manage'), fn ($query) => $query->whereHas('student.studentProfile', fn ($profile) => $profile->whereIn('school_class_id', $visibleClassIds)))
                ->orderByDesc('general_points')
                ->limit(10)
                ->get();

            if ($user->isRole('superadmin')) {
                $podiumClasses = SchoolClass::query()->with('academicYear')->orderBy('grade_level')->orderBy('name')->get();
                $podiumClassId = $request->integer('podium_class') ?: null;
                if ($podiumClassId && ! $podiumClasses->contains('id', $podiumClassId)) {
                    $podiumClassId = null;
                }
                $podium = StudentPointSummary::query()
                    ->with('student.studentProfile.schoolClass')
                    ->whereHas('student', fn ($query) => $query->where('role', 'student')
                        ->whereHas('studentProfile', fn ($profile) => $profile->whereNotNull('school_class_id')
                            ->when($podiumClassId, fn ($classQuery) => $classQuery->where('school_class_id', $podiumClassId))))
                    ->where('general_points', '>', 0)
                    ->orderByDesc('general_points')->orderBy('student_user_id')
                    ->limit(10)->get();
            }
        }

        $children = $user->isRole('parent')
            ? $user->children()->with(['studentProfile.schoolClass', 'pointSummary'])->get()
            : collect();

        $profileStudents = $user->isRole('student') ? collect([$user]) : $children;
        $semesters = collect();
        $selectedSemester = null;
        $profileClasses = collect();
        $selectedClassId = $request->integer('class') ?: null;
        $progress = collect();
        $todayAttendance = null;
        $attendanceSetting = null;

        if ($profileStudents->isNotEmpty()) {
            $semesters = Semester::query()->with('academicYear')->orderByDesc('starts_on')->get();
            $selectedSemester = $semesters->firstWhere('id', $request->integer('semester'))
                ?? $semesters->firstWhere('is_active', true)
                ?? $semesters->first();

            $studentIds = $profileStudents->pluck('id');
            $classIds = $profileStudents->pluck('studentProfile.school_class_id')
                ->merge(StudentClassHistory::query()->whereIn('student_user_id', $studentIds)->pluck('school_class_id'))
                ->filter()
                ->unique();
            $profileClasses = SchoolClass::query()->whereIn('id', $classIds)->orderBy('name')->get();

            if ($selectedClassId && ! $classIds->contains($selectedClassId)) {
                $selectedClassId = null;
            }

            $progress = $profileStudents->mapWithKeys(fn (User $student) => [
                $student->id => $progressService->summarize($student, $selectedSemester, $selectedClassId),
            ]);
        }

        if ($user->isRole('student')) {
            $todayAttendance = Attendance::query()
                ->where('student_user_id', $user->id)
                ->whereDate('attendance_date', today())
                ->first();
            $attendanceSetting = SchoolSetting::active();
        }

        return view('dashboard', [
            'user' => $user,
            'students' => $students,
            'children' => $children,
            'stats' => $stats,
            'leaderboard' => $leaderboard,
            'classProgress' => $classProgress,
            'podium' => $podium,
            'podiumClasses' => $podiumClasses,
            'podiumClassId' => $podiumClassId,
            'semesters' => $semesters,
            'selectedSemester' => $selectedSemester,
            'profileClasses' => $profileClasses,
            'selectedClassId' => $selectedClassId,
            'progress' => $progress,
            'todayAttendance' => $todayAttendance,
            'attendanceSetting' => $attendanceSetting,
        ]);
    }
}
