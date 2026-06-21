<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\StudentPointSummary;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $students = User::query()
            ->where('role', 'student')
            ->with(['studentProfile.schoolClass', 'pointSummary'])
            ->orderBy('name')
            ->get();

        $children = $user->isRole('parent')
            ? $user->children()->with(['studentProfile.schoolClass', 'pointSummary'])->get()
            : collect();

        return view('dashboard', [
            'user' => $user,
            'students' => $students,
            'children' => $children,
            'stats' => [
                'students' => User::query()->where('role', 'student')->count(),
                'teachers' => User::query()->where('role', 'teacher')->count(),
                'parents' => User::query()->where('role', 'parent')->count(),
                'todayAttendance' => Attendance::query()->whereDate('attendance_date', today())->count(),
                'priority' => StudentPointSummary::query()->where('label', 'Prioritas Perhatian Guru')->count(),
            ],
            'leaderboard' => StudentPointSummary::query()
                ->with('student.studentProfile.schoolClass')
                ->join('users', 'users.id', '=', 'student_point_summaries.student_user_id')
                ->orderByDesc('general_points')
                ->select('student_point_summaries.*')
                ->limit(10)
                ->get(),
        ]);
    }
}
