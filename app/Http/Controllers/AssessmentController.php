<?php

namespace App\Http\Controllers;

use App\Models\AchievementAssessment;
use App\Models\AttitudeAssessment;
use App\Models\Subject;
use App\Models\User;
use App\Services\PointCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AssessmentController extends Controller
{
    public function create()
    {
        return view('teacher.assessments', [
            'students' => User::query()->where('role', 'student')->with('studentProfile.schoolClass')->orderBy('name')->get(),
            'subjects' => Subject::query()->orderBy('name')->get(),
        ]);
    }

    public function storeAttitude(Request $request, PointCalculationService $points)
    {
        $data = $request->validate([
            'student_user_id' => ['required', 'exists:users,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'aspect' => ['required', 'string', 'max:100'],
            'score' => ['required', 'integer', 'min:1', 'max:5'],
            'notes' => ['nullable', 'string'],
        ]);

        $student = User::query()->where('role', 'student')->findOrFail($data['student_user_id']);
        $pointValue = $points->attitudePoints((int) $data['score']);

        $assessment = AttitudeAssessment::query()->create([
            ...$data,
            'teacher_user_id' => $request->user()->id,
            'school_class_id' => $student->studentProfile?->school_class_id,
            'points' => $pointValue,
            'assessed_on' => Carbon::today(),
        ]);

        $points->record($student, 'attitude', $pointValue, "Penilaian sikap: {$data['aspect']}", $request->user(), $assessment);

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

        $assessment = AchievementAssessment::query()->create([
            ...$data,
            'teacher_user_id' => $request->user()->id,
            'school_class_id' => $student->studentProfile?->school_class_id,
            'awarded_on' => Carbon::today(),
        ]);

        $points->record($student, 'achievement', (int) $data['points'], $data['title'], $request->user(), $assessment);

        return back()->with('status', 'Poin prestasi/pelanggaran berhasil disimpan.');
    }
}
