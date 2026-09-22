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

        if ($user->isRole('student')) {
            $user->loadMissing('pointSummary');
        }

        if (! in_array($user->role, ['student', 'parent'], true)) {
            $students = User::query()
                ->where('role', 'student')
                ->with(['studentProfile.schoolClass', 'pointSummary'])
                ->orderBy('name')
                ->get();

            $stats = [
                'students' => User::query()->where('role', 'student')->count(),
                'teachers' => User::query()->where('role', 'teacher')->count(),
                'parents' => User::query()->where('role', 'parent')->count(),
                'todayAttendance' => Attendance::query()->whereDate('attendance_date', today())->count(),
                'priority' => StudentPointSummary::query()->where('label', 'Prioritas Perhatian Guru')->count(),
            ];

            $leaderboard = StudentPointSummary::query()
                ->with('student.studentProfile.schoolClass')
                ->orderByDesc('general_points')
                ->limit(10)
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
