<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Semester;
use App\Models\StudentClassHistory;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AttendanceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_recap_can_be_searched_and_filtered(): void
    {
        $admin = User::query()->create([
            'name' => 'Admin', 'email' => 'admin-filter@test.test', 'role' => 'superadmin',
            'password' => 'password', 'is_active' => true, 'must_change_password' => false,
        ]);
        $class = SchoolClass::query()->create(['name' => 'VIII A']);
        $raka = User::query()->create(['name' => 'Raka Terlambat', 'email' => 'raka@test.test', 'role' => 'student', 'password' => 'password', 'is_active' => true]);
        $siti = User::query()->create(['name' => 'Siti Tepat Waktu', 'email' => 'siti@test.test', 'role' => 'student', 'password' => 'password', 'is_active' => true]);
        StudentProfile::query()->create(['user_id' => $raka->id, 'school_class_id' => $class->id]);
        StudentProfile::query()->create(['user_id' => $siti->id, 'school_class_id' => $class->id]);

        Attendance::query()->create(['student_user_id' => $raka->id, 'school_class_id' => $class->id, 'attendance_date' => today(), 'checked_in_at' => '07:15:00', 'status' => 'present', 'source' => 'web', 'is_ontime' => false]);
        Attendance::query()->create(['student_user_id' => $siti->id, 'school_class_id' => $class->id, 'attendance_date' => today(), 'checked_in_at' => '06:45:00', 'status' => 'present', 'source' => 'iot', 'is_ontime' => true]);

        $this->actingAs($admin)->get(route('attendance.index', [
            'q' => 'Raka', 'class' => $class->id, 'source' => 'web', 'status' => 'late',
        ]))->assertOk()->assertSee('Raka Terlambat')->assertSee('Terlambat')->assertDontSee('Siti Tepat Waktu');
    }

    public function test_second_check_in_does_not_overwrite_first_check_in_or_duplicate_points(): void
    {
        SchoolSetting::query()->create([
            'school_name' => 'Sekolah Test',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'attendance_radius_meters' => 100,
            'start_time' => '07:00',
            'late_after' => '07:00',
        ]);
        $student = User::query()->create([
            'name' => 'Siswa', 'email' => 'student@test.test', 'role' => 'student', 'password' => 'password', 'is_active' => true,
        ]);

        Carbon::setTestNow('2026-09-21 06:30:00');
        $this->actingAs($student)->post(route('attendance.check-in'), [
            'location_timestamp' => now()->getTimestampMs(),
            'latitude' => -6.2, 'longitude' => 106.8, 'accuracy' => 10,
        ])->assertSessionHasNoErrors();

        Carbon::setTestNow('2026-09-21 08:00:00');
        $this->actingAs($student)->post(route('attendance.check-in'), [
            'location_timestamp' => now()->getTimestampMs(),
            'latitude' => -6.2, 'longitude' => 106.8, 'accuracy' => 10,
        ])->assertSessionHas('status', 'Absensi hari ini sudah tercatat sebelumnya.');

        $this->assertDatabaseCount('attendances', 1);
        $this->assertDatabaseHas('attendances', ['checked_in_at' => '06:30:00', 'status' => 'present']);
        $this->assertDatabaseCount('point_transactions', 1);
        $this->assertDatabaseHas('student_point_summaries', ['student_user_id' => $student->id, 'attendance_points' => 5]);
    }

    public function test_check_in_outside_school_radius_is_rejected(): void
    {
        SchoolSetting::query()->create([
            'school_name' => 'Sekolah Test',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'attendance_radius_meters' => 50,
            'start_time' => '07:00',
            'late_after' => '07:00',
        ]);
        $student = User::query()->create([
            'name' => 'Siswa', 'email' => 'student2@test.test', 'role' => 'student', 'password' => 'password', 'is_active' => true,
        ]);

        $this->actingAs($student)->post(route('attendance.check-in'), [
            'latitude' => -6.3, 'longitude' => 106.9, 'accuracy' => 10,
            'location_timestamp' => now()->getTimestampMs(),
        ])->assertSessionHasErrors('latitude');

        $this->assertDatabaseCount('attendances', 0);
        $this->assertDatabaseCount('point_transactions', 0);
    }

    public function test_check_in_inside_maximum_radius_is_accepted(): void
    {
        SchoolSetting::query()->create([
            'school_name' => 'Sekolah Test', 'latitude' => -6.2, 'longitude' => 106.8,
            'attendance_radius_meters' => 50, 'max_location_accuracy_meters' => 30,
            'attendance_open_time' => '05:00', 'start_time' => '07:00',
            'late_after' => '07:00', 'attendance_close_time' => '10:00',
        ]);
        $student = User::query()->create([
            'name' => 'Siswa', 'email' => 'inside@test.test', 'role' => 'student', 'password' => 'password', 'is_active' => true,
        ]);
        Carbon::setTestNow('2026-09-22 06:30:00');

        $this->actingAs($student)->post(route('attendance.check-in'), [
            'latitude' => -6.1997, 'longitude' => 106.8, 'accuracy' => 12,
            'location_timestamp' => now()->getTimestampMs(),
        ])->assertSessionHasNoErrors();

        $attendance = Attendance::query()->sole();
        $this->assertLessThanOrEqual(50, $attendance->distance_meters);
        $this->assertSame(12, $attendance->location_accuracy_meters);
    }

    public function test_check_in_with_inaccurate_gps_is_rejected(): void
    {
        SchoolSetting::query()->create([
            'school_name' => 'Sekolah Test', 'latitude' => -6.2, 'longitude' => 106.8,
            'attendance_radius_meters' => 100, 'max_location_accuracy_meters' => 25,
            'attendance_open_time' => '05:00', 'start_time' => '07:00',
            'late_after' => '07:00', 'attendance_close_time' => '10:00',
        ]);
        $student = User::query()->create([
            'name' => 'Siswa', 'email' => 'inaccurate@test.test', 'role' => 'student', 'password' => 'password', 'is_active' => true,
        ]);
        Carbon::setTestNow('2026-09-22 06:30:00');

        $this->actingAs($student)->post(route('attendance.check-in'), [
            'latitude' => -6.2, 'longitude' => 106.8, 'accuracy' => 80,
            'location_timestamp' => now()->getTimestampMs(),
        ])->assertSessionHasErrors('accuracy');

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_check_in_outside_configured_time_window_is_rejected(): void
    {
        SchoolSetting::query()->create([
            'school_name' => 'Sekolah Test',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'attendance_radius_meters' => 50,
            'attendance_open_time' => '05:00',
            'start_time' => '07:00',
            'late_after' => '07:00',
            'attendance_close_time' => '10:00',
        ]);
        $student = User::query()->create([
            'name' => 'Siswa', 'email' => 'student3@test.test', 'role' => 'student', 'password' => 'password', 'is_active' => true,
        ]);
        Carbon::setTestNow('2026-09-21 23:00:00');

        $this->actingAs($student)->post(route('attendance.check-in'), [
            'location_timestamp' => now()->getTimestampMs(),
            'latitude' => -6.2, 'longitude' => 106.8, 'accuracy' => 10,
        ])->assertSessionHasErrors('attendance');

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_check_in_saves_class_and_semester_snapshot(): void
    {
        SchoolSetting::query()->create([
            'school_name' => 'Sekolah Test', 'latitude' => -6.2, 'longitude' => 106.8,
            'attendance_radius_meters' => 50, 'attendance_open_time' => '05:00',
            'start_time' => '07:00', 'late_after' => '07:00', 'attendance_close_time' => '10:00',
        ]);
        $year = AcademicYear::query()->create(['name' => '2026/2027', 'is_active' => true]);
        $semester = Semester::query()->create([
            'academic_year_id' => $year->id, 'name' => 'Ganjil', 'starts_on' => '2026-07-01', 'ends_on' => '2026-12-31', 'is_active' => true,
        ]);
        $class = SchoolClass::query()->create(['name' => 'X A', 'academic_year_id' => $year->id]);
        $student = User::query()->create([
            'name' => 'Siswa', 'email' => 'snapshot@test.test', 'role' => 'student', 'password' => 'password', 'is_active' => true,
        ]);
        StudentProfile::query()->create(['user_id' => $student->id, 'school_class_id' => $class->id, 'nis' => 'S-1']);
        StudentClassHistory::query()->create([
            'student_user_id' => $student->id, 'school_class_id' => $class->id,
            'academic_year_id' => $year->id, 'started_on' => '2026-07-01',
        ]);
        Carbon::setTestNow('2026-09-21 06:30:00');

        $this->actingAs($student)->post(route('attendance.check-in'), [
            'location_timestamp' => now()->getTimestampMs(),
            'latitude' => -6.2, 'longitude' => 106.8, 'accuracy' => 10,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('attendances', [
            'student_user_id' => $student->id,
            'school_class_id' => $class->id,
            'academic_year_id' => $year->id,
            'semester_id' => $semester->id,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
