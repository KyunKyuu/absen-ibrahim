<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendancePermission;
use App\Models\GradeAssessment;
use App\Models\PointTransaction;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Semester;
use App\Models\StudentClassHistory;
use App\Models\StudentPointSummary;
use App\Models\User;
use App\Services\StudentProgressService;
use Carbon\Carbon;
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
        $attendanceBreakdown = [
            'ontime' => 0,
            'late' => 0,
            'excused' => 0,
            'absent' => 0,
            'unrecorded' => 0,
            'total' => 0,
        ];
        $levelDistribution = [
            'teladan' => 0,
            'berkembang' => 0,
            'pemula' => 0,
            'perhatian' => 0,
        ];
        $weeklyAttendanceTrend = collect();
        $recentActivityFeed = collect();
        $attendanceSetting = SchoolSetting::active();

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

            $todayAttendances = Attendance::query()->whereDate('attendance_date', today())
                ->when(! $user->canDo('school.manage'), fn ($query) => $query->whereIn('student_user_id', $students->pluck('id')))
                ->get();

            $ontimeCount = $todayAttendances->where('status', 'present')->where('is_ontime', true)->count();
            $lateCount = $todayAttendances->filter(fn ($a) => $a->status === 'late' || ($a->status === 'present' && ! $a->is_ontime))->count();
            $excusedCount = $todayAttendances->where('status', 'excused')->count();
            $absentCount = $todayAttendances->where('status', 'absent')->count();
            $totalStudentsCount = $students->count();
            $unrecordedCount = max(0, $totalStudentsCount - $todayAttendances->count());

            $attendanceBreakdown = [
                'ontime' => $ontimeCount,
                'late' => $lateCount,
                'excused' => $excusedCount,
                'absent' => $absentCount,
                'unrecorded' => $unrecordedCount,
                'total' => $totalStudentsCount,
            ];

            foreach ($students as $student) {
                $pts = (int) ($student->pointSummary?->general_points ?? 0);
                if ($pts >= 100) {
                    $levelDistribution['teladan']++;
                } elseif ($pts >= 50) {
                    $levelDistribution['berkembang']++;
                } elseif ($pts >= 0) {
                    $levelDistribution['pemula']++;
                } else {
                    $levelDistribution['perhatian']++;
                }
            }

            $weeklyAttendanceTrend = Attendance::query()
                ->when(! $user->canDo('school.manage'), fn ($query) => $query->whereIn('student_user_id', $students->pluck('id')))
                ->selectRaw('DATE(attendance_date) as att_date, count(*) as total, sum(case when is_ontime = 1 then 1 else 0 end) as ontime_count')
                ->groupBy('att_date')
                ->orderByDesc('att_date')
                ->limit(6)
                ->get()
                ->sortBy('att_date')
                ->values()
                ->map(fn ($row) => [
                    'date' => Carbon::parse($row->att_date)->translatedFormat('d M'),
                    'total' => (int) $row->total,
                    'ontime' => (int) $row->ontime_count,
                ]);

            $recentActivityFeed = PointTransaction::query()
                ->when(! $user->canDo('school.manage'), fn ($query) => $query->whereIn('student_user_id', $students->pluck('id')))
                ->with(['student.studentProfile.schoolClass', 'actor'])
                ->latest()
                ->limit(6)
                ->get();
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
        $reportGrades = collect();
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

            $reportGrades = $profileStudents->mapWithKeys(function (User $student) use ($selectedSemester, $selectedClassId) {
                $assessments = GradeAssessment::query()
                    ->where('semester_id', $selectedSemester?->id)
                    ->whereHas('grades', fn ($query) => $query->where('student_user_id', $student->id))
                    ->when($selectedClassId, fn ($query) => $query->where('school_class_id', $selectedClassId))
                    ->with([
                        'subject',
                        'grades' => fn ($query) => $query->where('student_user_id', $student->id),
                    ])
                    ->orderBy('subject_id')
                    ->orderBy('assessed_on')
                    ->get();

                return [$student->id => $assessments->groupBy(fn ($assessment) => $assessment->subject?->name ?? 'Mata pelajaran')];
            });
        }

        if ($user->isRole('student')) {
            $todayAttendance = Attendance::query()
                ->where('student_user_id', $user->id)
                ->whereDate('attendance_date', today())
                ->first();
            $attendanceSetting = SchoolSetting::active();
        }

        $pendingPermitsCount = 0;
        if (! in_array($user->role, ['student', 'parent'], true)) {
            $pendingPermitsCount = AttendancePermission::query()
                ->when($user->isRole('teacher') && ! $user->canDo('school.manage'), function ($q) use ($user) {
                    $q->whereIn('school_class_id', $user->homeroomClasses()->pluck('id'));
                })
                ->where('status', 'pending')
                ->count();
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
            'reportGrades' => $reportGrades,
            'todayAttendance' => $todayAttendance,
            'attendanceSetting' => $attendanceSetting,
            'attendanceBreakdown' => $attendanceBreakdown,
            'levelDistribution' => $levelDistribution,
            'weeklyAttendanceTrend' => $weeklyAttendanceTrend,
            'recentActivityFeed' => $recentActivityFeed,
            'pendingPermitsCount' => $pendingPermitsCount,
        ]);
    }
}
