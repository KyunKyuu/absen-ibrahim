<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IotAttendanceLog;
use App\Models\IotDevice;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class IotAttendanceController extends Controller
{
    public function store(Request $request, AttendanceService $attendanceService)
    {
        $data = $request->validate([
            'device_identifier' => ['required', 'string'],
            'api_token' => ['required', 'string'],
            'fingerprint_user_id' => ['required', 'string'],
            'scanned_at' => ['nullable', 'date'],
        ]);

        $device = IotDevice::query()
            ->where('identifier', $data['device_identifier'])
            ->where('is_active', true)
            ->first();

        if (! $device || ! Hash::check($data['api_token'], $device->api_token_hash)) {
            return response()->json(['message' => 'Device tidak valid.'], 401);
        }

        $student = User::query()
            ->where('role', 'student')
            ->whereHas('studentProfile', fn ($query) => $query->where('nis', $data['fingerprint_user_id']))
            ->first();

        $log = IotAttendanceLog::query()->create([
            'iot_device_id' => $device->id,
            'device_identifier' => $data['device_identifier'],
            'fingerprint_user_id' => $data['fingerprint_user_id'],
            'student_user_id' => $student?->id,
            'scanned_at' => isset($data['scanned_at']) ? Carbon::parse($data['scanned_at']) : now(),
            'status' => $student ? 'matched' : 'unmatched',
            'payload' => $request->all(),
            'message' => $student ? 'Absensi diterima.' : 'Fingerprint ID belum terhubung ke NIS siswa.',
        ]);

        $device->update(['last_seen_at' => now()]);

        if (! $student) {
            return response()->json([
                'message' => $log->message,
                'log_id' => $log->id,
            ], 422);
        }

        $attendance = $attendanceService->checkInFromDevice(
            $student,
            $log->scanned_at,
            $device->identifier
        );

        return response()->json([
            'message' => 'Absensi fingerprint berhasil direkam.',
            'attendance_id' => $attendance->id,
            'student' => $student->name,
        ]);
    }
}
