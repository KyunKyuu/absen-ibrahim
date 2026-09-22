<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function store(Request $request, AttendanceService $attendanceService)
    {
        abort_unless($request->user()->isRole('student'), 403);

        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy' => ['required', 'numeric', 'min:0', 'max:10000'],
        ]);

        $attendance = $attendanceService->checkInFromWeb(
            $request->user(),
            (float) $data['latitude'],
            (float) $data['longitude'],
            (float) $data['accuracy'],
        );

        return back()->with('status', $attendance->wasRecentlyCreated
            ? 'Absensi berhasil direkam.'
            : 'Absensi hari ini sudah tercatat sebelumnya.');
    }

    public function index(Request $request, string $scope = 'all')
    {
        abort_unless(in_array($scope, ['all', 'today'], true), 404);

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'class' => ['nullable', 'integer', 'exists:school_classes,id'],
            'source' => ['nullable', 'in:web,iot'],
            'status' => ['nullable', 'string', 'max:30'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        return view('attendance.index', [
            'scope' => $scope,
            'filters' => $filters,
            'classes' => SchoolClass::query()->orderBy('name')->get(),
            'attendances' => Attendance::query()
                ->with('student.studentProfile.schoolClass')
                ->when($scope === 'today', fn ($query) => $query->whereDate('attendance_date', today()))
                ->when($filters['q'] ?? null, fn ($query, $search) => $query->whereHas('student', fn ($student) => $student->where('name', 'like', "%{$search}%")))
                ->when($filters['class'] ?? null, fn ($query, $classId) => $query->where('school_class_id', $classId))
                ->when($filters['source'] ?? null, fn ($query, $source) => $query->where('source', $source))
                ->when(($filters['status'] ?? null) === 'late', fn ($query) => $query->where('status', 'present')->where('is_ontime', false))
                ->when(($filters['status'] ?? null) === 'present', fn ($query) => $query->where('status', 'present')->where('is_ontime', true))
                ->when(in_array($filters['status'] ?? null, ['excused', 'absent'], true), fn ($query) => $query->where('status', $filters['status']))
                ->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('attendance_date', '>=', $date))
                ->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('attendance_date', '<=', $date))
                ->latest('attendance_date')
                ->latest('checked_in_at')
                ->paginate(30)
                ->withQueryString(),
        ]);
    }
}
