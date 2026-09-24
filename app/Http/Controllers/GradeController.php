<?php

namespace App\Http\Controllers;

use App\Models\GradeAssessment;
use App\Models\Semester;
use App\Models\StudentGrade;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class GradeController extends Controller
{
    private function assignments(User $user)
    {
        return TeachingAssignment::query()->where('teacher_user_id', $user->id)->whereNotNull('subject_id');
    }

    private function canEdit(User $user, GradeAssessment $assessment): bool
    {
        return $assessment->teacher_user_id === $user->id && $this->assignments($user)
            ->where('school_class_id', $assessment->school_class_id)->where('subject_id', $assessment->subject_id)->exists();
    }

    public function index(Request $request, string $section = 'list')
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'semester_id' => ['nullable', 'integer', 'exists:semesters,id'],
        ]);
        $assessments = GradeAssessment::query()->with(['schoolClass', 'subject', 'semester.academicYear', 'teacher'])->withCount('grades')
            ->when(! $request->user()->canDo('school.manage'), function ($query) use ($request) {
                $query->where('teacher_user_id', $request->user()->id)->whereExists(function ($assignments) {
                    $assignments->selectRaw('1')->from('teaching_assignments')
                        ->whereColumn('teaching_assignments.teacher_user_id', 'grade_assessments.teacher_user_id')
                        ->whereColumn('teaching_assignments.school_class_id', 'grade_assessments.school_class_id')
                        ->whereColumn('teaching_assignments.subject_id', 'grade_assessments.subject_id');
                });
            })
            ->when($filters['q'] ?? null, fn ($query, $q) => $query->where('title', 'like', "%{$q}%"))
            ->when($filters['semester_id'] ?? null, fn ($query, $id) => $query->where('semester_id', $id))
            ->latest('assessed_on')->latest('id')->paginate(20)->withQueryString();

        return view('teacher.grades.index', [
            'section' => $section,
            'assessments' => $assessments, 'filters' => $filters,
            'assignments' => $this->assignments($request->user())->with(['schoolClass.academicYear', 'subject'])->get(),
            'semesters' => Semester::query()->with('academicYear')->orderByDesc('starts_on')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'assignment_id' => ['required', 'integer'],
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'title' => ['required', 'string', 'max:150'],
            'kind' => ['required', Rule::in(array_keys(GradeAssessment::KINDS))],
            'assessed_on' => ['required', 'date_format:Y-m-d'],
        ]);
        $assignment = $this->assignments($request->user())->with('schoolClass')->find($data['assignment_id']);
        abort_unless($assignment, 403, 'Anda tidak ditugaskan mengajar mata pelajaran di kelas ini.');
        $semester = Semester::query()->findOrFail($data['semester_id']);
        abort_unless($semester->is_active, 422, 'Hanya semester aktif yang dapat digunakan untuk penilaian baru.');
        if ($assignment->schoolClass->academic_year_id !== $semester->academic_year_id
            || ($semester->starts_on && $data['assessed_on'] < $semester->starts_on->toDateString())
            || ($semester->ends_on && $data['assessed_on'] > $semester->ends_on->toDateString())) {
            throw ValidationException::withMessages(['semester_id' => 'Semester dan tanggal penilaian harus sesuai tahun ajaran kelas.']);
        }
        $assessment = GradeAssessment::query()->create([
            'teacher_user_id' => $request->user()->id, 'school_class_id' => $assignment->school_class_id,
            'subject_id' => $assignment->subject_id, 'semester_id' => $semester->id,
            'kind' => $data['kind'], 'title' => $data['title'], 'assessed_on' => $data['assessed_on'],
        ]);

        return redirect()->route('teacher.grades.show', $assessment)->with('status', 'Penilaian dibuat. Silakan isi nilai siswa.');
    }

    public function show(Request $request, GradeAssessment $assessment)
    {
        $canEdit = $this->canEdit($request->user(), $assessment);
        abort_unless($canEdit || $request->user()->canDo('school.manage'), 403);
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $students = User::query()->where(function ($query) use ($assessment) {
            $query->where(fn ($current) => $current->where('role', 'student')->where('is_active', true)
                ->whereHas('studentProfile', fn ($profile) => $profile->where('school_class_id', $assessment->school_class_id)))
                ->orWhereIn('id', $assessment->grades()->select('student_user_id'));
        })->with('studentProfile')->when($filters['q'] ?? null, fn ($query, $q) => $query->where(fn ($matches) => $matches
            ->where('name', 'like', "%{$q}%")->orWhereHas('studentProfile', fn ($profile) => $profile->where('nis', 'like', "%{$q}%"))))
            ->orderBy('name')->orderBy('id')->paginate(20)->withQueryString();

        return view('teacher.grades.show', [
            'assessment' => $assessment->load(['subject', 'schoolClass', 'semester.academicYear', 'teacher']),
            'students' => $students, 'canEdit' => $canEdit, 'filters' => $filters,
            'grades' => $assessment->grades()->whereIn('student_user_id', $students->pluck('id'))->get()->keyBy('student_user_id'),
        ]);
    }

    public function save(Request $request, GradeAssessment $assessment)
    {
        abort_unless($this->canEdit($request->user(), $assessment), 403);
        $data = $request->validate([
            'grades' => ['required', 'array', 'min:1', 'max:20'],
            'grades.*.student_user_id' => ['required', 'integer', 'distinct'],
            'grades.*.score' => ['nullable', 'numeric', 'between:0,100', 'decimal:0,2'],
            'grades.*.notes' => ['nullable', 'string', 'max:1000'],
        ]);
        $rows = collect($data['grades'])->filter(fn ($row) => isset($row['score']) && $row['score'] !== '');
        if ($rows->isEmpty()) {
            throw ValidationException::withMessages(['grades' => 'Isi setidaknya satu nilai. Nilai 0 tetap dapat disimpan.']);
        }
        DB::transaction(function () use ($rows, $assessment, $request) {
            $assignment = $this->assignments($request->user())->where('school_class_id', $assessment->school_class_id)
                ->where('subject_id', $assessment->subject_id)->lockForUpdate()->first();
            abort_unless($assignment, 403);
            $ids = $rows->pluck('student_user_id');
            $students = User::query()->whereIn('id', $ids)->where('role', 'student')->where('is_active', true)
                ->whereHas('studentProfile', fn ($profile) => $profile->where('school_class_id', $assessment->school_class_id))->get();
            if ($students->count() !== $ids->count()) {
                throw ValidationException::withMessages(['grades' => 'Siswa harus aktif dan masih terdaftar di kelas penilaian ini. Muat ulang daftar siswa.']);
            }
            foreach ($rows as $row) {
                StudentGrade::query()->updateOrCreate([
                    'grade_assessment_id' => $assessment->id, 'student_user_id' => $row['student_user_id'],
                ], ['score' => $row['score'], 'notes' => $row['notes'] ?? null]);
            }
        });

        return back()->with('status', $rows->count().' nilai siswa berhasil disimpan.');
    }
}
