<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AttendanceGpsSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function student(): User
    {
        Carbon::setTestNow('2026-09-22 06:30:00');
        SchoolSetting::query()->create([
            'school_name' => 'Sekolah GPS', 'latitude' => -6.2, 'longitude' => 106.8,
            'attendance_radius_meters' => 100, 'max_location_accuracy_meters' => 30,
            'attendance_open_time' => '05:00', 'start_time' => '07:00',
            'late_after' => '07:00', 'attendance_close_time' => '10:00',
        ]);

        return User::query()->create([
            'name' => 'Siswa GPS', 'username' => 'gps-test', 'role' => 'student',
            'password' => 'password', 'is_active' => true, 'must_change_password' => false,
        ]);
    }

    private function location(array $overrides = []): array
    {
        return array_replace([
            'latitude' => -6.2, 'longitude' => 106.8, 'accuracy' => 10,
            'location_timestamp' => now()->getTimestampMs(),
        ], $overrides);
    }

    public static function invalidLocations(): array
    {
        return [
            'no latitude' => [['latitude' => null], 'latitude'],
            'bad longitude' => [['longitude' => 181], 'longitude'],
            'bad latitude' => [['latitude' => 91], 'latitude'],
            'non numeric' => [['latitude' => 'NaN'], 'latitude'],
            'zero accuracy' => [['accuracy' => 0], 'accuracy'],
            'negative accuracy' => [['accuracy' => -1], 'accuracy'],
            'coarse location' => [['accuracy' => 31], 'accuracy'],
            'outside' => [['latitude' => -6.3], 'latitude'],
            'uncertainty crosses radius' => [['latitude' => -6.1993, 'accuracy' => 25], 'accuracy'],
            'missing timestamp' => [['location_timestamp' => null], 'location_timestamp'],
            'expired timestamp' => [['location_timestamp' => 1], 'location_timestamp'],
        ];
    }

    #[DataProvider('invalidLocations')]
    public function test_invalid_location_never_creates_attendance_or_points(array $input, string $error): void
    {
        $student = $this->student();
        $this->actingAs($student)->post(route('attendance.check-in'), $this->location($input))
            ->assertSessionHasErrors($error);
        $this->assertDatabaseCount('attendances', 0);
        $this->assertDatabaseCount('point_transactions', 0);
    }

    public function test_stale_and_future_location_timestamps_are_rejected(): void
    {
        $student = $this->student();
        foreach ([-61000, 11000] as $offset) {
            $this->actingAs($student)->post(route('attendance.check-in'), $this->location([
                'location_timestamp' => now()->getTimestampMs() + $offset,
            ]))->assertSessionHasErrors('location_timestamp');
        }
        $this->assertDatabaseCount('attendances', 0);
    }

    public static function clockBoundaries(): array
    {
        return [
            'before opening' => ['04:59:59', null],
            'at opening' => ['05:00:00', 'present'],
            'at ontime limit' => ['07:00:00', 'present'],
            'after ontime limit' => ['07:00:01', 'late'],
            'at closing' => ['10:00:00', 'late'],
            'after closing' => ['10:00:01', null],
        ];
    }

    #[DataProvider('clockBoundaries')]
    public function test_schedule_uses_server_clock_with_normalized_times(string $time, ?string $expected): void
    {
        $student = $this->student();
        Carbon::setTestNow('2026-09-22 '.$time);
        $response = $this->actingAs($student)->post(route('attendance.check-in'), $this->location());
        if ($expected === null) {
            $response->assertSessionHasErrors('attendance');
            $this->assertDatabaseCount('attendances', 0);
            $this->assertDatabaseCount('point_transactions', 0);
        } else {
            $response->assertSessionHasNoErrors();
            $this->assertDatabaseHas('attendances', ['status' => $expected, 'checked_in_at' => $time]);
            $this->assertDatabaseHas('point_transactions', ['points' => $expected === 'present' ? 5 : 1]);
        }
    }

    public function test_forged_identity_time_and_status_are_ignored(): void
    {
        $student = $this->student();
        Carbon::setTestNow('2026-09-22 08:00:00');
        $this->actingAs($student)->post(route('attendance.check-in'), $this->location([
            'student_user_id' => 987654, 'attendance_date' => '2026-01-01',
            'checked_in_at' => '06:00:00', 'is_ontime' => true, 'status' => 'present', 'source' => 'fingerprint',
        ]))->assertSessionHasNoErrors();
        $this->assertDatabaseHas('attendances', [
            'student_user_id' => $student->id,
            'checked_in_at' => '08:00:00', 'status' => 'late', 'source' => 'web',
        ]);
        $this->assertSame('2026-09-22', Attendance::query()->sole()->attendance_date->toDateString());
        $this->assertDatabaseCount('point_transactions', 1);
    }

    public function test_missing_school_location_is_rejected(): void
    {
        $student = $this->student();
        SchoolSetting::query()->update(['latitude' => null, 'longitude' => null]);
        $this->actingAs($student)->post(route('attendance.check-in'), $this->location())
            ->assertSessionHasErrors('latitude');
        $this->assertDatabaseCount('attendances', 0);
        $this->get(route('dashboard'))->assertOk()->assertSee('Titik absensi sekolah belum diatur');
    }

    public function test_production_requires_https_and_accepts_https(): void
    {
        $student = $this->student();
        $this->app->instance('env', 'production');
        $this->actingAs($student)->withSession(['_token' => 'gps-test-token'])
            ->post('http://localhost/attendance/check-in', $this->location(['_token' => 'gps-test-token']))
            ->assertSessionHasErrors('attendance');
        $this->assertDatabaseCount('attendances', 0);
        $this->flushSession();
        $this->actingAs($student)->withSession(['_token' => 'gps-test-token'])
            ->post('https://localhost/attendance/check-in', $this->location(['_token' => 'gps-test-token']))
            ->assertSessionHasNoErrors();
        $this->assertDatabaseCount('attendances', 1);
    }

    public function test_production_rejects_check_in_without_csrf_token(): void
    {
        $student = $this->student();
        $this->app->instance('env', 'production');
        $this->actingAs($student)->post('https://localhost/attendance/check-in', $this->location())->assertStatus(419);
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_inactive_student_cannot_check_in(): void
    {
        $student = $this->student();
        $student->update(['is_active' => false]);
        $this->actingAs($student)->post(route('attendance.check-in'), $this->location())->assertRedirect(route('login'));
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_requests_are_throttled_without_duplicate_attendance(): void
    {
        $student = $this->student();
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->actingAs($student)->post(route('attendance.check-in'), $this->location())->assertSessionHasNoErrors();
        }
        $this->post(route('attendance.check-in'), $this->location())->assertStatus(429);
        $this->assertDatabaseCount('attendances', 1);
        $this->assertDatabaseCount('point_transactions', 1);
    }

    public function test_recap_finds_canonical_late_status(): void
    {
        $student = $this->student();
        $oldClass = SchoolClass::query()->create(['name' => 'Kelas Saat Absen']);
        $newClass = SchoolClass::query()->create(['name' => 'Kelas Baru']);
        $profile = StudentProfile::query()->create(['user_id' => $student->id, 'school_class_id' => $oldClass->id]);
        Carbon::setTestNow('2026-09-22 08:00:00');
        $this->actingAs($student)->post(route('attendance.check-in'), $this->location())->assertSessionHasNoErrors();
        $profile->update(['school_class_id' => $newClass->id]);
        $admin = User::query()->create(['name' => 'Admin', 'role' => 'superadmin', 'password' => 'password', 'is_active' => true]);
        $this->actingAs($admin)->get(route('attendance.index', ['status' => 'late']))
            ->assertOk()->assertSee('Siswa GPS')->assertSee('Terlambat')->assertSee('±10 m')
            ->assertSee('Kelas Saat Absen');
        $report = $this->get(route('reports.attendance'))->assertOk()->streamedContent();
        $this->assertStringContainsString('Kelas Saat Absen', $report);
        $this->assertStringNotContainsString('Kelas Baru', $report);
        $this->assertStringContainsString('Galat GPS Meter', $report);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
