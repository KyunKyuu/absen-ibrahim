<?php

namespace App\Http\Controllers;

use App\Models\AchievementAssessment;
use App\Models\AttitudeAssessment;
use App\Models\AssessmentMaster;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\TeachingAssignment;
use App\Models\User;
use App\Models\TeacherAttitudeCredit;
use App\Services\PointCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssessmentController extends Controller
{
    public function create(Request $request, string $section = 'attitude')
    {
        abort_unless(in_array($section, ['attitude', 'achievement'], true), 404);

        $classes = SchoolClass::query()
            ->when(! $request->user()->canDo('school.manage'), fn ($query) => $query->where(function ($assigned) use ($request) {
                $assigned->whereHas('teachingAssignments', fn ($assignments) => $assignments->where('teacher_user_id', $request->user()->id));
            }))
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();
        $selectedClassId = (int) $request->integer('class', $classes->first()?->id ?? 0);
        if (! $classes->contains('id', $selectedClassId)) {
            $selectedClassId = (int) ($classes->first()?->id ?? 0);
        }

        return view('teacher.assessments', [
            'section' => $section,
            'classes' => $classes,
            'selectedClassId' => $selectedClassId,
            'students' => User::query()->where('role', 'student')
                ->whereHas('studentProfile', fn ($profile) => $profile->where('school_class_id', $selectedClassId))
                ->with(['studentProfile.schoolClass', 'pointSummary'])
                ->orderBy('name')
                ->get(),
            'subjects' => Subject::query()
                ->when(! $request->user()->canDo('school.manage'), fn ($query) => $query->whereHas('teachingAssignments', fn ($assignments) => $assignments
                    ->where('teacher_user_id', $request->user()->id)
                    ->where('school_class_id', $selectedClassId)))
                ->orderBy('name')->get(),
            'semesters' => Semester::query()->where('academic_year_id', $classes->firstWhere('id', $selectedClassId)?->academic_year_id)->where('is_active', true)->orderBy('starts_on')->get(),
            'attitudeCreditBalance' => $this->attitudeCreditBalance($request->user(), $selectedClassId),
            'attitudeMasters' => AssessmentMaster::query()->where('kind', 'attitude')->where('is_active', true)->orderBy('name')->get(),
            'achievementMasters' => AssessmentMaster::query()->where('kind', 'achievement')->where('is_active', true)->orderBy('group_name')->orderBy('name')->get(),
            'violationMasters' => AssessmentMaster::query()->where('kind', 'violation')->where('is_active', true)->orderBy('group_name')->orderBy('name')->get(),
        ]);
    }

    private function attitudeCreditBalance(User $teacher, int $classId, ?int $subjectId = null, ?int $semesterId = null): int
    {
        if (! $classId || ! $teacher->isRole('teacher')) {
            return 100;
        }

        $assignment = TeachingAssignment::query()->where('teacher_user_id', $teacher->id)
            ->where('school_class_id', $classId)
            ->when($subjectId, fn ($query) => $query->where('subject_id', $subjectId))
            ->first();
        if (! $assignment) {
            return 0;
        }

        $allocation = TeacherAttitudeCredit::query()->where('teacher_user_id', $teacher->id)->value('credits') ?? 100;
        return max(0, $allocation - (int) AttitudeAssessment::query()
            ->where('teacher_user_id', $teacher->id)
            ->where('school_class_id', $classId)
            ->where('subject_id', $assignment->subject_id)
            ->when($semesterId, fn ($query) => $query->where('semester_id', $semesterId))
            ->sum('credit_cost'));
    }

    public function storeAttitude(Request $request, PointCalculationService $points)
    {
        $data = $request->validate([
            'student_user_id' => ['required', 'exists:users,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'aspect' => ['required', 'string', 'max:100'],
            'score' => ['required', 'integer', 'min:1', 'max:5'],
            'credit_cost' => ['required', 'integer', 'min:1', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $student = User::query()->where('role', 'student')->findOrFail($data['student_user_id']);

        $teacher = $request->user();
        $classId = $student->studentProfile?->school_class_id;
        $assignment = TeachingAssignment::query()->where('teacher_user_id', $teacher->id)->where('school_class_id', $classId)->where('subject_id', $data['subject_id'])->exists();
        abort_unless($teacher->canDo('school.manage') || $assignment, 403);
        $semester = Semester::query()->where('is_active', true)->findOrFail($data['semester_id']);
        $schoolClass = SchoolClass::query()->findOrFail($classId);
        abort_unless((int) $semester->academic_year_id === (int) $schoolClass->academic_year_id, 422);
        $pointValue = $points->attitudePoints((int) $data['score']);

        DB::transaction(function () use ($data, $student, $pointValue, $request, $points, $classId, $teacher) {
            $used = AttitudeAssessment::query()->where('teacher_user_id', $teacher->id)->where('school_class_id', $classId)->where('subject_id', $data['subject_id'])->where('semester_id', $data['semester_id'])->lockForUpdate()->sum('credit_cost');
            $allocation = TeacherAttitudeCredit::query()->where('teacher_user_id', $teacher->id)->value('credits') ?? 100;
            if ($used + (int) $data['credit_cost'] > $allocation) {
                throw ValidationException::withMessages(['credit_cost' => 'Kredit penilaian semester ini tidak cukup. Sisa kredit: '.max(0, $allocation - $used).'.']);
            }
            $assessment = AttitudeAssessment::query()->create([
                ...$data,
                'teacher_user_id' => $request->user()->id,
                'school_class_id' => $classId,
                'points' => $pointValue,
                'assessed_on' => Carbon::today(),
            ]);

            $points->record($student, 'attitude', $pointValue, "Penilaian sikap: {$data['aspect']}", $request->user(), $assessment);
        });

        return back()->with('status', 'Penilaian sikap berhasil disimpan.');
    }

    public function storeAchievement(Request $request, PointCalculationService $points)
    {
        $data = $request->validate([
            'student_user_id' => ['required', 'exists:users,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'title' => ['required', 'string', 'max:150'],
            'category' => ['required', 'in:achievement,violation'],
            'points' => ['required', 'integer', 'min:-100', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $student = User::query()->where('role', 'student')->findOrFail($data['student_user_id']);

        $master = AssessmentMaster::query()
            ->where('kind', $data['category'])
            ->where('name', $data['title'])
            ->where('is_active', true)
            ->first();
        if ($master?->points !== null) {
            $data['points'] = (int) $master->points;
        }

        if (($data['category'] === 'achievement' && $data['points'] <= 0)
            || ($data['category'] === 'violation' && $data['points'] >= 0)) {
            throw ValidationException::withMessages([
                'points' => 'Prestasi harus berpoin positif dan pelanggaran harus berpoin negatif.',
            ]);
        }

        if ($data['category'] === 'achievement'
            && (($student->pointSummary?->general_points ?? 0) + (int) $data['points']) > 100) {
            throw ValidationException::withMessages([
                'points' => 'Penambahan poin tidak dapat membuat total poin siswa melebihi 100.',
            ]);
        }

        DB::transaction(function () use ($data, $student, $request, $points) {
            $assessment = AchievementAssessment::query()->create([
                ...$data,
                'teacher_user_id' => $request->user()->id,
                'school_class_id' => $student->studentProfile?->school_class_id,
                'awarded_on' => Carbon::today(),
            ]);

            $points->record($student, 'achievement', (int) $data['points'], $data['title'], $request->user(), $assessment);
        });

        return back()->with('status', 'Poin prestasi/pelanggaran berhasil disimpan.');
    }
}
