<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function store(Request $request, AttendanceService $attendanceService)
    {
        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $attendanceService->checkInFromWeb($request->user(), (float) $data['latitude'], (float) $data['longitude']);

        return back()->with('status', 'Absensi berhasil direkam.');
    }

    public function index()
    {
        return view('attendance.index', [
            'attendances' => Attendance::query()
                ->with('student.studentProfile.schoolClass')
                ->latest('attendance_date')
                ->paginate(30),
        ]);
    }
}
