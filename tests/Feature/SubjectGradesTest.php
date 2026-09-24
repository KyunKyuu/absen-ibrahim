<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\GradeAssessment;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\StudentGrade;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectGradesTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        $teacher = User::factory()->create(['name' => 'Guru Inggris', 'role' => 'teacher', 'is_active' => true, 'must_change_password' => false]);
        $other = User::factory()->create(['role' => 'teacher', 'is_active' => true, 'must_change_password' => false]);
        $year = AcademicYear::create(['name' => '2026/2027']);
        $semester = Semester::create(['academic_year_id' => $year->id, 'name' => 'Ganjil', 'starts_on' => '2026-07-01', 'ends_on' => '2026-12-31']);
        $class = SchoolClass::create(['academic_year_id' => $year->id, 'name' => '11A']);
        $otherClass = SchoolClass::create(['academic_year_id' => $year->id, 'name' => '12A', 'homeroom_teacher_id' => $teacher->id]);
        $english = Subject::create(['name' => 'Bahasa Inggris']);
        $math = Subject::create(['name' => 'Matematika']);
        $assignment = TeachingAssignment::create(['teacher_user_id' => $teacher->id, 'school_class_id' => $class->id, 'subject_id' => $english->id]);
        $otherAssignment = TeachingAssignment::create(['teacher_user_id' => $other->id, 'school_class_id' => $class->id, 'subject_id' => $math->id]);
        $student = User::factory()->create(['name' => 'Siswa Sebelas', 'role' => 'student', 'is_active' => true, 'must_change_password' => false]);
        StudentProfile::create(['user_id' => $student->id, 'school_class_id' => $class->id, 'nis' => '11001']);
        $outsider = User::factory()->create(['role' => 'student', 'is_active' => true]);
        StudentProfile::create(['user_id' => $outsider->id, 'school_class_id' => $otherClass->id]);
        $assessment = GradeAssessment::create(['teacher_user_id' => $teacher->id, 'school_class_id' => $class->id, 'subject_id' => $english->id, 'semester_id' => $semester->id, 'title' => 'Grammar 1', 'kind' => 'quiz', 'assessed_on' => '2026-09-22']);

        return compact('teacher', 'other', 'semester', 'class', 'otherClass', 'assignment', 'otherAssignment', 'student', 'outsider', 'assessment');
    }

    public function test_teacher_can_create_only_from_own_assignment(): void
    {
        $f = $this->fixture();
        $payload = ['assignment_id' => $f['assignment']->id, 'semester_id' => $f['semester']->id, 'title' => 'Speaking', 'kind' => 'assignment', 'assessed_on' => '2026-09-22'];
        $this->actingAs($f['teacher'])->get(route('teacher.grades.create'))->assertOk()->assertSee('Bahasa Inggris')->assertDontSee('Matematika');
        $this->post(route('teacher.grades.store'), $payload)->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('grade_assessments', ['title' => 'Speaking', 'subject_id' => $f['assignment']->subject_id]);
        $this->post(route('teacher.grades.store'), array_replace($payload, ['assignment_id' => $f['otherAssignment']->id]))->assertForbidden();
        $this->assertDatabaseCount('grade_assessments', 2);
    }

    public function test_grades_update_without_duplicates_and_zero_is_a_valid_score(): void
    {
        $f = $this->fixture();
        foreach ([0, 87.5] as $score) {
            $this->actingAs($f['teacher'])->post(route('teacher.grades.save', $f['assessment']), ['grades' => [['student_user_id' => $f['student']->id, 'score' => $score, 'notes' => 'Speaking']]])->assertSessionHasNoErrors();
            $this->assertSame(number_format($score, 2, '.', ''), StudentGrade::sole()->score);
        }
        $this->assertDatabaseCount('student_grades', 1);
        $this->assertSame('87.50', StudentGrade::sole()->score);
        $this->assertDatabaseCount('point_transactions', 0);
        $this->get(route('teacher.grades.show', $f['assessment']))->assertOk()->assertSee('87.50');
    }

    public function test_other_teacher_cannot_read_or_write_even_with_same_class_assignment(): void
    {
        $f = $this->fixture();
        $this->actingAs($f['other'])->get(route('teacher.grades.index'))->assertOk()->assertDontSee('Grammar 1');
        $this->get(route('teacher.grades.show', $f['assessment']))->assertForbidden();
        $this->post(route('teacher.grades.save', $f['assessment']), ['grades' => [['student_user_id' => $f['student']->id, 'score' => 80]]])->assertForbidden();
        $this->assertDatabaseCount('student_grades', 0);
    }

    public function test_wrong_class_student_rejects_whole_batch(): void
    {
        $f = $this->fixture();
        $this->actingAs($f['teacher'])->post(route('teacher.grades.save', $f['assessment']), ['grades' => [
            ['student_user_id' => $f['student']->id, 'score' => 80], ['student_user_id' => $f['outsider']->id, 'score' => 90],
        ]])->assertSessionHasErrors('grades');
        $this->assertDatabaseCount('student_grades', 0);
    }

    public function test_score_range_and_semester_are_validated(): void
    {
        $f = $this->fixture();
        $this->actingAs($f['teacher']);
        foreach ([-1, 101, 'abc', '90.123'] as $score) {
            $this->post(route('teacher.grades.save', $f['assessment']), ['grades' => [['student_user_id' => $f['student']->id, 'score' => $score]]])->assertSessionHasErrors('grades.0.score');
        }
        $this->post(route('teacher.grades.store'), ['assignment_id' => $f['assignment']->id, 'semester_id' => $f['semester']->id, 'title' => 'Wrong date', 'kind' => 'quiz', 'assessed_on' => '2027-01-01'])->assertSessionHasErrors('semester_id');
        $this->assertDatabaseCount('student_grades', 0);
    }

    public function test_revoked_assignment_blocks_edit_and_existing_grades_are_preserved(): void
    {
        $f = $this->fixture();
        StudentGrade::create(['grade_assessment_id' => $f['assessment']->id, 'student_user_id' => $f['student']->id, 'score' => 70]);
        $f['class']->update(['homeroom_teacher_id' => $f['teacher']->id]);
        $f['assignment']->delete();
        $this->actingAs($f['teacher'])->post(route('teacher.grades.save', $f['assessment']), ['grades' => [['student_user_id' => $f['student']->id, 'score' => 90]]])->assertForbidden();
        $this->assertSame('70.00', StudentGrade::sole()->score);
    }

    public function test_students_cannot_access_teacher_grading_routes(): void
    {
        $f = $this->fixture();
        $this->actingAs($f['student'])->get(route('teacher.grades.index'))->assertForbidden();
        $this->post(route('teacher.grades.save', $f['assessment']), ['grades' => [['student_user_id' => $f['student']->id, 'score' => 100]]])->assertForbidden();
    }

    public function test_student_roster_is_paginated_and_searchable(): void
    {
        $f = $this->fixture();
        foreach (range(1, 22) as $i) {
            $student = User::factory()->create(['role' => 'student', 'is_active' => true]);
            StudentProfile::create(['user_id' => $student->id, 'school_class_id' => $f['class']->id]);
        }
        $this->actingAs($f['teacher'])->get(route('teacher.grades.show', $f['assessment']))->assertOk()
            ->assertViewHas('students', fn ($students) => $students->total() === 23 && $students->count() === 20);
        $this->get(route('teacher.grades.show', [$f['assessment'], 'q' => '11001']))->assertOk()
            ->assertViewHas('students', fn ($students) => $students->total() === 1)->assertSee('Siswa Sebelas');
    }

    public function test_moved_student_scores_remain_readable_but_cannot_be_changed(): void
    {
        $f = $this->fixture();
        StudentGrade::create(['grade_assessment_id' => $f['assessment']->id, 'student_user_id' => $f['student']->id, 'score' => 75]);
        $f['student']->studentProfile->update(['school_class_id' => $f['otherClass']->id]);
        $this->actingAs($f['teacher'])->get(route('teacher.grades.show', $f['assessment']))->assertOk()->assertSee('75.00')->assertSee('Arsip');
        $this->post(route('teacher.grades.save', $f['assessment']), ['grades' => [['student_user_id' => $f['student']->id, 'score' => 100]]])->assertSessionHasErrors('grades');
        $this->assertSame('75.00', StudentGrade::sole()->score);
    }

    public function test_admin_can_monitor_but_cannot_impersonate_assigned_teacher(): void
    {
        $f = $this->fixture();
        $admin = User::factory()->create(['role' => 'superadmin', 'is_active' => true, 'must_change_password' => false]);
        $this->actingAs($admin)->get(route('teacher.grades.show', $f['assessment']))->assertOk()->assertSee('Mode baca');
        $this->post(route('teacher.grades.save', $f['assessment']), ['grades' => [['student_user_id' => $f['student']->id, 'score' => 100]]])->assertForbidden();
    }

    public function test_empty_score_does_not_delete_existing_grade(): void
    {
        $f = $this->fixture();
        StudentGrade::create(['grade_assessment_id' => $f['assessment']->id, 'student_user_id' => $f['student']->id, 'score' => 75]);
        $this->actingAs($f['teacher'])->post(route('teacher.grades.save', $f['assessment']), ['grades' => [['student_user_id' => $f['student']->id, 'score' => '']]])->assertSessionHasErrors('grades');
        $this->assertSame('75.00', StudentGrade::sole()->score);
    }
}
