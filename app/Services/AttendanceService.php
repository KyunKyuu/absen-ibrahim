<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\SchoolSetting;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    public function __construct(
        private readonly GeoDistanceService $geoDistance,
        private readonly PointCalculationService $points,
    ) {}

    public function checkInFromWeb(User $student, float $latitude, float $longitude): Attendance
    {
        $setting = SchoolSetting::active();

        if ($setting->latitude === null || $setting->longitude === null) {
            throw ValidationException::withMessages([
                'latitude' => 'Lokasi sekolah belum diatur admin.',
            ]);
        }

        $distance = $this->geoDistance->meters((float) $setting->latitude, (float) $setting->longitude, $latitude, $longitude);
        $withinRadius = $distance <= $setting->attendance_radius_meters;

        if (! $withinRadius) {
            throw ValidationException::withMessages([
                'latitude' => "Lokasi Anda berjarak {$distance} meter dari sekolah.",
            ]);
        }

        return $this->createAttendance($student, Carbon::now(), 'web', [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'distance_meters' => $distance,
            'is_within_radius' => true,
        ], $student);
    }

    public function checkInFromDevice(User $student, CarbonInterface $scannedAt, string $deviceIdentifier): Attendance
    {
        return $this->createAttendance($student, $scannedAt, 'fingerprint', [
            'device_identifier' => $deviceIdentifier,
            'is_within_radius' => true,
            'notes' => 'Absensi dari perangkat fingerprint IoT.',
        ]);
    }

    private function createAttendance(User $student, CarbonInterface $time, string $source, array $extra, ?User $actor = null): Attendance
    {
        $setting = SchoolSetting::active();
        $isOntime = $time->format('H:i:s') <= $setting->late_after;
        $status = $isOntime ? 'present' : 'late';

        $attendance = Attendance::query()->firstOrNew([
            'student_user_id' => $student->id,
            'attendance_date' => $time->toDateString(),
        ]);
        $isFirstCheckIn = ! $attendance->exists;

        $attendance->fill(array_merge($extra, [
            'created_by_user_id' => $actor?->id,
            'checked_in_at' => $time->format('H:i:s'),
            'status' => $status,
            'source' => $source,
            'is_ontime' => $isOntime,
        ]));
        $attendance->save();

        if ($isFirstCheckIn) {
            $this->points->record(
                $student,
                'attendance',
                $this->points->attendancePoints($status, $isOntime),
                $isOntime ? 'Absensi hadir tepat waktu' : 'Absensi terlambat',
                $actor,
                $attendance
            );
        }

        return $attendance;
    }
}
