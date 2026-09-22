<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\IotAttendanceLog;
use App\Models\IotDevice;
use App\Models\SchoolSetting;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IotAttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_fingerprint_device_can_create_attendance_for_student(): void
    {
        SchoolSetting::query()->create([
            'school_name' => 'Sekolah Test',
            'attendance_radius_meters' => 100,
            'start_time' => '07:00',
            'late_after' => '07:00',
        ]);

        $student = User::query()->create([
            'name' => 'Raka Pramudya',
            'email' => 'raka@example.test',
            'role' => 'student',
            'password' => Hash::make('password123'),
        ]);

        StudentProfile::query()->create([
            'user_id' => $student->id,
            'nis' => 'FP-1001',
        ]);

        IotDevice::query()->create([
            'name' => 'Fingerprint Gerbang',
            'identifier' => 'FP-GATE-01',
            'api_token_hash' => Hash::make('secret-token'),
        ]);

        $response = $this->postJson('/api/iot/attendance', [
            'device_identifier' => 'FP-GATE-01',
            'api_token' => 'secret-token',
            'fingerprint_user_id' => 'FP-1001',
            'scanned_at' => '2026-06-21T23:45:00Z',
        ]);

        $response->assertOk()
            ->assertJsonPath('student', 'Raka Pramudya');

        $this->assertDatabaseHas('attendances', [
            'student_user_id' => $student->id,
            'source' => 'fingerprint',
            'status' => 'present',
            'is_ontime' => true,
        ]);

        $this->assertDatabaseHas('student_point_summaries', [
            'student_user_id' => $student->id,
            'attendance_points' => 5,
            'general_points' => 5,
        ]);

        $log = IotAttendanceLog::query()->sole();
        $this->assertArrayNotHasKey('api_token', $log->payload);
        $this->assertSame('2026-06-22', Attendance::query()->sole()->attendance_date->toDateString());
    }
}
