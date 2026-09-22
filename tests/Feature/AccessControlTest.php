<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    public function test_deactivated_authenticated_user_is_logged_out(): void
    {
        $user = User::query()->create([
            'name' => 'Nonaktif', 'email' => 'inactive@test.test', 'role' => 'student', 'password' => 'password', 'is_active' => false,
        ]);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_student_cannot_record_a_payment(): void
    {
        $student = User::query()->create([
            'name' => 'Siswa', 'email' => 'student@test.test', 'role' => 'student', 'password' => 'password', 'is_active' => true,
        ]);

        $this->actingAs($student)->post(route('finance.fee-types.store'), [
            'name' => 'SPP', 'code' => 'SPP', 'billing_cycle' => 'monthly', 'default_amount' => 1000,
        ])->assertForbidden();
    }

    public function test_non_student_cannot_use_student_check_in_endpoint(): void
    {
        $teacher = User::query()->create([
            'name' => 'Guru', 'email' => 'teacher-checkin@test.test', 'role' => 'teacher',
            'password' => 'password', 'is_active' => true,
        ]);

        $this->actingAs($teacher)->post(route('attendance.check-in'), [
            'latitude' => -6.2, 'longitude' => 106.8, 'accuracy' => 10,
        ])->assertForbidden();
    }
}
