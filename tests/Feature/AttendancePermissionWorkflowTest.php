<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendancePermission;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Semester;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttendancePermissionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function basicFixture(): array
    {
        SchoolSetting::query()->create([
            'school_name' => 'Sekolah Islam Terpadu',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'attendance_radius_meters' => 100,
            'start_time' => '07:00',
            'late_after' => '07:00',
        ]);

        $year = AcademicYear::query()->create([
            'name' => '2026/2027',
            'starts_on' => '2026-07-01',
            'ends_on' => '2027-06-30',
            'is_active' => true,
        ]);

        $semester = Semester::query()->create([
            'academic_year_id' => $year->id,
            'name' => 'Ganjil',
            'starts_on' => '2026-07-01',
            'ends_on' => '2026-12-31',
            'is_active' => true,
        ]);

        $teacher = User::query()->create([
            'name' => 'Ustadz Ahmad',
            'email' => 'ahmad-wali@test.test',
            'role' => 'teacher',
            'password' => 'password',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $class = SchoolClass::query()->create([
            'name' => 'VIII A',
            'academic_year_id' => $year->id,
            'homeroom_teacher_id' => $teacher->id,
        ]);

        $student = User::query()->create([
            'name' => 'Fatih Santri',
            'email' => 'fatih@test.test',
            'role' => 'student',
            'password' => 'password',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        StudentProfile::query()->create([
            'user_id' => $student->id,
            'school_class_id' => $class->id,
        ]);

        $parent = User::query()->create([
            'name' => 'Bapak Fatih',
            'email' => 'bapak-fatih@test.test',
            'role' => 'parent',
            'password' => 'password',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        $parent->children()->attach($student->id, ['relationship' => 'father']);

        $admin = User::query()->create([
            'name' => 'Admin Sekolah',
            'email' => 'admin-sekolah@test.test',
            'role' => 'superadmin',
            'password' => 'password',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        return [$student, $parent, $teacher, $admin, $class, $semester];
    }

    public function test_student_can_submit_sick_leave_request(): void
    {
        Storage::fake('local');
        [$student, , , , $class] = $this->basicFixture();

        $file = UploadedFile::fake()->create('surat-dokter.pdf', 300, 'application/pdf');

        $response = $this->actingAs($student)->post(route('attendance.permissions.store'), [
            'type' => 'sick',
            'start_date' => '2026-09-28',
            'end_date' => '2026-09-29',
            'reason' => 'Demam tinggi dan disarankan dokter istirahat di rumah.',
            'attachment' => $file,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('attendance_permissions', [
            'student_user_id' => $student->id,
            'submitted_by_user_id' => $student->id,
            'school_class_id' => $class->id,
            'type' => 'sick',
            'status' => 'pending',
        ]);

        $permit = AttendancePermission::first();
        $this->assertNotNull($permit->attachment_path);
        $this->assertEquals('2026-09-28', $permit->start_date->format('Y-m-d'));
        $this->assertEquals('2026-09-29', $permit->end_date->format('Y-m-d'));
        Storage::disk('local')->assertExists($permit->attachment_path);
    }

    public function test_parent_can_submit_leave_for_their_child(): void
    {
        [$student, $parent, , , $class] = $this->basicFixture();

        $response = $this->actingAs($parent)->post(route('attendance.permissions.store'), [
            'student_user_id' => $student->id,
            'type' => 'excused',
            'start_date' => '2026-09-30',
            'end_date' => '2026-09-30',
            'reason' => 'Menghadiri acara pernikahan keluarga di luar kota.',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('attendance_permissions', [
            'student_user_id' => $student->id,
            'submitted_by_user_id' => $parent->id,
            'school_class_id' => $class->id,
            'type' => 'excused',
            'status' => 'pending',
        ]);

        $permit = AttendancePermission::where('submitted_by_user_id', $parent->id)->first();
        $this->assertEquals('2026-09-30', $permit->start_date->format('Y-m-d'));
    }

    public function test_parent_cannot_submit_leave_for_other_student(): void
    {
        [, $parent] = $this->basicFixture();

        $otherStudent = User::query()->create([
            'name' => 'Siswa Lain',
            'email' => 'other-student@test.test',
            'role' => 'student',
            'password' => 'password',
            'is_active' => true,
        ]);

        $this->actingAs($parent)->post(route('attendance.permissions.store'), [
            'student_user_id' => $otherStudent->id,
            'type' => 'sick',
            'start_date' => '2026-09-28',
            'end_date' => '2026-09-28',
            'reason' => 'Sakit flu.',
        ])->assertNotFound();
    }

    public function test_homeroom_teacher_can_approve_leave_and_it_records_attendance(): void
    {
        [$student, , $teacher, , $class, $semester] = $this->basicFixture();

        $permission = AttendancePermission::query()->create([
            'student_user_id' => $student->id,
            'submitted_by_user_id' => $student->id,
            'school_class_id' => $class->id,
            'academic_year_id' => $class->academic_year_id,
            'semester_id' => $semester->id,
            'type' => 'sick',
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-02',
            'reason' => 'Sakit demam.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($teacher)->post(route('attendance.permissions.approve', $permission), [
            'notes' => 'Semoga lekas sembuh, santri teladan.',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals('approved', $permission->fresh()->status);
        $this->assertEquals($teacher->id, $permission->fresh()->reviewed_by_user_id);
        $this->assertEquals('Semoga lekas sembuh, santri teladan.', $permission->fresh()->review_notes);

        // Check attendance records for both days
        $this->assertDatabaseHas('attendances', [
            'student_user_id' => $student->id,
            'status' => 'sick',
            'source' => 'permit',
        ]);
        $this->assertEquals(2, Attendance::query()->where('student_user_id', $student->id)->where('status', 'sick')->count());
    }

    public function test_unauthorized_teacher_cannot_approve_leave(): void
    {
        [$student, , , , $class, $semester] = $this->basicFixture();

        $otherTeacher = User::query()->create([
            'name' => 'Guru Lain',
            'email' => 'other-teacher@test.test',
            'role' => 'teacher',
            'password' => 'password',
            'is_active' => true,
        ]);

        $permission = AttendancePermission::query()->create([
            'student_user_id' => $student->id,
            'submitted_by_user_id' => $student->id,
            'school_class_id' => $class->id,
            'academic_year_id' => $class->academic_year_id,
            'semester_id' => $semester->id,
            'type' => 'excused',
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-01',
            'reason' => 'Izin keluarga.',
            'status' => 'pending',
        ]);

        $this->actingAs($otherTeacher)
            ->post(route('attendance.permissions.approve', $permission))
            ->assertForbidden();

        $this->assertEquals('pending', $permission->fresh()->status);
    }

    public function test_submitter_can_cancel_pending_leave(): void
    {
        [$student, , , , $class, $semester] = $this->basicFixture();

        $permission = AttendancePermission::query()->create([
            'student_user_id' => $student->id,
            'submitted_by_user_id' => $student->id,
            'school_class_id' => $class->id,
            'academic_year_id' => $class->academic_year_id,
            'semester_id' => $semester->id,
            'type' => 'excused',
            'start_date' => '2026-10-05',
            'end_date' => '2026-10-05',
            'reason' => 'Salah tanggal izin.',
            'status' => 'pending',
        ]);

        $this->actingAs($student)
            ->delete(route('attendance.permissions.cancel', $permission))
            ->assertRedirect();

        $this->assertDatabaseMissing('attendance_permissions', ['id' => $permission->id]);
    }

    public function test_attachment_download_is_secured(): void
    {
        Storage::fake('local');
        [$student, $parent, $teacher, $admin] = $this->basicFixture();

        $file = UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf');
        $path = $file->store('attendance-permissions', 'local');

        $permission = AttendancePermission::query()->create([
            'student_user_id' => $student->id,
            'submitted_by_user_id' => $parent->id,
            'type' => 'sick',
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-01',
            'reason' => 'Sakit.',
            'status' => 'pending',
            'attachment_path' => $path,
        ]);

        // Student, parent, homeroom teacher, and admin can view
        $this->actingAs($student)->get(route('attendance.permissions.attachment', $permission))->assertOk();
        $this->actingAs($parent)->get(route('attendance.permissions.attachment', $permission))->assertOk();
        $this->actingAs($admin)->get(route('attendance.permissions.attachment', $permission))->assertOk();

        // Stranger student cannot view
        $stranger = User::query()->create([
            'name' => 'Orang Lain',
            'email' => 'stranger@test.test',
            'role' => 'student',
            'password' => 'password',
            'is_active' => true,
        ]);
        $this->actingAs($stranger)->get(route('attendance.permissions.attachment', $permission))->assertForbidden();
    }
}
