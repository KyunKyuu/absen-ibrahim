<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IotAttendanceLog;
use App\Models\IotDevice;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
            ->where('is_active', true)
            ->whereHas('studentProfile', fn ($query) => $query->where('nis', $data['fingerprint_user_id']))
            ->first();

        $scannedAt = isset($data['scanned_at'])
            ? Carbon::parse($data['scanned_at'])->setTimezone(config('app.timezone'))
            : now();

        $log = IotAttendanceLog::query()->create([
            'iot_device_id' => $device->id,
            'device_identifier' => $data['device_identifier'],
            'fingerprint_user_id' => $data['fingerprint_user_id'],
            'student_user_id' => $student?->id,
            'scanned_at' => $scannedAt,
            'status' => $student ? 'matched' : 'unmatched',
            // Token perangkat tidak boleh masuk ke log/database.
            'payload' => Arr::except($data, 'api_token'),
            'message' => $student ? 'Absensi diterima.' : 'Fingerprint ID belum terhubung ke NIS siswa.',
        ]);

        $device->update(['last_seen_at' => now()]);

        if (! $student) {
            return response()->json([
                'message' => $log->message,
                'log_id' => $log->id,
            ], 422);
        }

        try {
            $attendance = $attendanceService->checkInFromDevice(
                $student,
                $log->scanned_at,
                $device->identifier
            );
        } catch (ValidationException $exception) {
            $log->update([
                'status' => 'rejected',
                'message' => collect($exception->errors())->flatten()->first() ?? 'Absensi ditolak.',
            ]);

            throw $exception;
        }

        return response()->json([
            'message' => 'Absensi fingerprint berhasil direkam.',
            'attendance_id' => $attendance->id,
            'student' => $student->name,
        ]);
    }
}
