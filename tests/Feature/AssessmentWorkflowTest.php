<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_selects_an_assigned_class_before_live_searching_students(): void
    {
        $teacher = User::query()->create(['name' => 'Guru Inggris', 'email' => 'english@test.test', 'role' => 'teacher', 'password' => 'password', 'is_active' => true]);
        $class11 = SchoolClass::query()->create(['name' => '11B', 'grade_level' => 11]);
        $class12 = SchoolClass::query()->create(['name' => '12A', 'grade_level' => 12]);
        $otherClass = SchoolClass::query()->create(['name' => '10C', 'grade_level' => 10]);
        $subject = Subject::query()->create(['name' => 'Bahasa Inggris', 'code' => 'BIG']);
        TeachingAssignment::query()->create(['teacher_user_id' => $teacher->id, 'school_class_id' => $class11->id, 'subject_id' => $subject->id]);
        TeachingAssignment::query()->create(['teacher_user_id' => $teacher->id, 'school_class_id' => $class12->id, 'subject_id' => $subject->id]);
        $student11 = User::query()->create(['name' => 'Siswa Sebelas', 'email' => '11@test.test', 'role' => 'student', 'password' => 'password', 'is_active' => true]);
        $student12 = User::query()->create(['name' => 'Siswa Dua Belas', 'email' => '12@test.test', 'role' => 'student', 'password' => 'password', 'is_active' => true]);
        StudentProfile::query()->create(['user_id' => $student11->id, 'school_class_id' => $class11->id, 'nis' => '1101']);
        StudentProfile::query()->create(['user_id' => $student12->id, 'school_class_id' => $class12->id, 'nis' => '1201']);

        $this->actingAs($teacher)->get(route('teacher.assessments', ['class' => $class11->id]))
            ->assertOk()
            ->assertSee('11B')
            ->assertSee('12A')
            ->assertDontSee('10C')
            ->assertSee('Siswa Sebelas')
            ->assertDontSee('Siswa Dua Belas')
            ->assertSee('id="student-search"', false);
    }

    public function test_violation_must_be_negative_and_achievement_must_be_positive(): void
    {
        $teacher = User::query()->create([
            'name' => 'Guru', 'email' => 'teacher@test.test', 'role' => 'teacher', 'password' => 'password', 'is_active' => true,
        ]);
        $student = User::query()->create([
            'name' => 'Siswa', 'email' => 'student@test.test', 'role' => 'student', 'password' => 'password', 'is_active' => true,
        ]);

        $this->actingAs($teacher)->post(route('teacher.assessments.achievement'), [
            'student_user_id' => $student->id,
            'title' => 'Pelanggaran',
            'category' => 'violation',
            'points' => 10,
        ])->assertSessionHasErrors('points');

        $this->assertDatabaseCount('achievement_assessments', 0);
        $this->assertDatabaseCount('point_transactions', 0);

        $this->actingAs($teacher)->post(route('teacher.assessments.achievement'), [
            'student_user_id' => $student->id,
            'title' => 'Pelanggaran',
            'category' => 'violation',
            'points' => -10,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('student_point_summaries', [
            'student_user_id' => $student->id,
            'achievement_points' => -10,
            'general_points' => -10,
        ]);
    }
}
