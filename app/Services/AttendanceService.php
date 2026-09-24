<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Semester;
use App\Models\StudentClassHistory;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    public function __construct(
        private readonly GeoDistanceService $geoDistance,
        private readonly PointCalculationService $points,
    ) {}

    public function checkInFromWeb(User $student, float $latitude, float $longitude, float $accuracy): Attendance
    {
        $setting = SchoolSetting::active();

        if (! is_finite($latitude) || ! is_finite($longitude) || ! is_finite($accuracy)
            || abs($latitude) > 90 || abs($longitude) > 180 || $accuracy <= 0) {
            throw ValidationException::withMessages(['latitude' => 'Pembacaan GPS tidak valid. Ambil lokasi kembali.']);
        }

        if ($setting->latitude === null || $setting->longitude === null) {
            throw ValidationException::withMessages([
                'latitude' => 'Lokasi sekolah belum diatur admin.',
            ]);
        }

        if ($accuracy > $setting->max_location_accuracy_meters) {
            throw ValidationException::withMessages([
                'accuracy' => 'Akurasi GPS masih ±'.(int) ceil($accuracy).' meter. Maksimal yang diizinkan ±'.$setting->max_location_accuracy_meters.' meter. Coba di area terbuka.',
            ]);
        }

        $distance = $this->geoDistance->meters((float) $setting->latitude, (float) $setting->longitude, $latitude, $longitude);
        $withinRadius = $distance <= $setting->attendance_radius_meters;

        if (! $withinRadius) {
            throw ValidationException::withMessages([
                'latitude' => "Lokasi Anda berjarak {$distance} meter dari sekolah.",
            ]);
        }

        if ($distance + $accuracy > $setting->attendance_radius_meters) {
            throw ValidationException::withMessages([
                'accuracy' => 'Lokasi masih terlalu dekat batas area dengan galat GPS saat ini. Dekati titik absensi sekolah dan tunggu GPS lebih akurat.',
            ]);
        }

        return $this->createAttendance($student, Carbon::now(), 'web', [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'distance_meters' => $distance,
            'location_accuracy_meters' => (int) ceil($accuracy),
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
        return DB::transaction(function () use ($student, $time, $source, $extra, $actor) {
            $existing = Attendance::query()
                ->where('student_user_id', $student->id)
                ->whereDate('attendance_date', $time->toDateString())
                ->lockForUpdate()
                ->first();

            // Check-in pertama adalah sumber kebenaran. Scan ulang tidak boleh
            // mengubah jam/status tetapi meninggalkan poin lama yang berbeda.
            if ($existing) {
                return $existing;
            }

            $setting = SchoolSetting::active();
            $checkInTime = $time->format('H:i:s');
            $openTime = Carbon::parse($setting->attendance_open_time)->format('H:i:s');
            $closeTime = Carbon::parse($setting->attendance_close_time)->format('H:i:s');
            $lateAfter = Carbon::parse($setting->late_after)->format('H:i:s');

            if ($checkInTime < $openTime || $checkInTime > $closeTime) {
                throw ValidationException::withMessages([
                    'attendance' => 'Absensi hanya dibuka pukul '.substr($setting->attendance_open_time, 0, 5).'–'.substr($setting->attendance_close_time, 0, 5).'.',
                ]);
            }

            $isOntime = $checkInTime <= $lateAfter;
            $status = $isOntime ? 'present' : 'late';
            $attendanceDate = $time->toDateString();
            $classHistory = StudentClassHistory::query()
                ->where('student_user_id', $student->id)
                ->whereDate('started_on', '<=', $attendanceDate)
                ->where(fn ($query) => $query->whereNull('ended_on')->orWhereDate('ended_on', '>=', $attendanceDate))
                ->latest('started_on')
                ->latest('id')
                ->first();
            $schoolClassId = $classHistory?->school_class_id
                ?? $student->studentProfile()->value('school_class_id');
            $academicYearId = $classHistory?->academic_year_id
                ?? ($schoolClassId ? SchoolClass::query()->whereKey($schoolClassId)->value('academic_year_id') : null);
            $semesterId = Semester::query()
                ->whereDate('starts_on', '<=', $attendanceDate)
                ->whereDate('ends_on', '>=', $attendanceDate)
                ->value('id');

            $attendance = Attendance::query()->createOrFirst([
                'student_user_id' => $student->id,
                'attendance_date' => $attendanceDate,
            ], array_merge($extra, [
                'school_class_id' => $schoolClassId,
                'academic_year_id' => $academicYearId,
                'semester_id' => $semesterId,
                'created_by_user_id' => $actor?->id,
                'checked_in_at' => $checkInTime,
                'status' => $status,
                'source' => $source,
                'is_ontime' => $isOntime,
            ]));

            if (! $attendance->wasRecentlyCreated) {
                return $attendance;
            }

            $this->points->record(
                $student,
                'attendance',
                $this->points->attendancePoints($status, $isOntime),
                $isOntime ? 'Absensi hadir tepat waktu' : 'Absensi terlambat',
                $actor,
                $attendance
            );

            return $attendance;
        });
    }
}
