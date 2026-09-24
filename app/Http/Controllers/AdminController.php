<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AchievementAssessment;
use App\Models\Attendance;
use App\Models\AttitudeAssessment;
use App\Models\GradeAssessment;
use App\Models\IotDevice;
use App\Models\ParentProfile;
use App\Models\Permission;
use App\Models\PointTransaction;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Semester;
use App\Models\StudentClassHistory;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\TeacherProfile;
use App\Models\TeachingAssignment;
use App\Models\User;
use App\Services\FinanceService;
use App\Services\GoogleSheetAccountImportService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function students(Request $request)
    {
        $user = $request->user();
        abort_unless($user->canDo('school.manage') || $user->canDo('assessments.manage'), 403);

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'class_id' => ['nullable', 'integer', 'exists:school_classes,id'],
            'per_page' => ['nullable', 'integer', Rule::in([20, 50, 100])],
        ]);
        $classIds = SchoolClass::query()
            ->when(! $user->canDo('school.manage'), fn ($query) => $query->where(function ($classes) use ($user) {
                $classes->where('homeroom_teacher_id', $user->id)
                    ->orWhereHas('teachingAssignments', fn ($assignments) => $assignments->where('teacher_user_id', $user->id));
            }))
            ->pluck('id');

        return view('students.index', [
            'filters' => $filters,
            'classes' => SchoolClass::query()->whereIn('id', $classIds)->orderBy('grade_level')->orderBy('name')->get(),
            'students' => User::query()->where('role', 'student')
                ->when(! $user->canDo('school.manage'), fn ($query) => $query->whereHas('studentProfile', fn ($profile) => $profile->whereIn('school_class_id', $classIds)))
                ->with(['studentProfile.schoolClass', 'pointSummary'])
                ->when($filters['q'] ?? null, function ($query, $search) {
                    $query->where(function ($matches) use ($search) {
                        $matches->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%")
                            ->orWhereHas('studentProfile', fn ($profile) => $profile->where('nis', 'like', "%{$search}%"));
                    });
                })
                ->when($filters['class_id'] ?? null, fn ($query, $classId) => $query->whereHas('studentProfile', fn ($profile) => $profile->where('school_class_id', $classId)))
                ->orderBy('name')->paginate((int) ($filters['per_page'] ?? 20))->withQueryString(),
        ]);
    }

    public function showStudent(Request $request, User $student)
    {
        $user = $request->user();
        abort_unless($user->canDo('school.manage') || $user->canDo('assessments.manage'), 403);
        abort_unless($student->role === 'student' && $student->studentProfile, 404);

        $classIds = SchoolClass::query()
            ->when(! $user->canDo('school.manage'), fn ($query) => $query->where(function ($classes) use ($user) {
                $classes->where('homeroom_teacher_id', $user->id)
                    ->orWhereHas('teachingAssignments', fn ($assignments) => $assignments->where('teacher_user_id', $user->id));
            }))
            ->pluck('id');
        abort_unless($user->canDo('school.manage') || $classIds->contains($student->studentProfile->school_class_id), 403);

        $student->load(['studentProfile.schoolClass.academicYear', 'pointSummary']);
        $allHistoryClassIds = StudentClassHistory::query()->where('student_user_id', $student->id)->pluck('school_class_id')
            ->push($student->studentProfile->school_class_id)->filter()->unique()->values();
        $availableClassIds = $user->canDo('school.manage') ? $allHistoryClassIds : $allHistoryClassIds->intersect($classIds)->values();
        $filter = $request->validate([
            'class_id' => ['nullable', 'integer', Rule::in($availableClassIds->all())],
            'attendance_month' => ['nullable', 'date_format:Y-m'],
        ]);
        $selectedClassId = isset($filter['class_id']) ? (int) $filter['class_id'] : null;
        $attendanceMonth = $filter['attendance_month'] ?? null;
        $classOptions = SchoolClass::query()->whereIn('id', $availableClassIds)->with('academicYear')
            ->orderBy('grade_level')->orderBy('name')->get();

        $attendanceQuery = Attendance::query()->where('student_user_id', $student->id)
            ->when($selectedClassId, fn ($query) => $query->where('school_class_id', $selectedClassId))
            ->when($attendanceMonth, function ($query) use ($attendanceMonth) {
                $month = CarbonImmutable::createFromFormat('!Y-m', $attendanceMonth);
                $query->whereBetween('attendance_date', [$month->startOfMonth(), $month->endOfMonth()]);
            });
        $attendanceMonths = Attendance::query()->where('student_user_id', $student->id)
            ->when($selectedClassId, fn ($query) => $query->where('school_class_id', $selectedClassId))
            ->orderByDesc('attendance_date')->pluck('attendance_date')->filter()
            ->map(fn ($date) => CarbonImmutable::parse($date)->format('Y-m'))->unique()->values();
        $attendanceCount = (clone $attendanceQuery)->count();
        $presentCount = (clone $attendanceQuery)->whereIn('status', ['present', 'late'])->count();
        $attendances = (clone $attendanceQuery)->orderByDesc('attendance_date')->paginate(10, ['*'], 'attendance_page')->withQueryString();

        $transactionsQuery = PointTransaction::query()->where('student_user_id', $student->id)
            ->when($selectedClassId, function ($query) use ($selectedClassId) {
                $query->where(function ($events) use ($selectedClassId) {
                    $events->where(fn ($source) => $source->where('source_type', Attendance::class)->whereIn('source_id', Attendance::query()->where('school_class_id', $selectedClassId)->select('id')))
                        ->orWhere(fn ($source) => $source->where('source_type', AttitudeAssessment::class)->whereIn('source_id', AttitudeAssessment::query()->where('school_class_id', $selectedClassId)->select('id')))
                        ->orWhere(fn ($source) => $source->where('source_type', AchievementAssessment::class)->whereIn('source_id', AchievementAssessment::query()->where('school_class_id', $selectedClassId)->select('id')));
                });
            });
        $transactions = $transactionsQuery->latest()->paginate(10, ['*'], 'activity_page')->withQueryString();
        $attitudeAssessments = AttitudeAssessment::query()->where('student_user_id', $student->id)
            ->when($selectedClassId, fn ($query) => $query->where('school_class_id', $selectedClassId))
            ->with('subject')->latest('assessed_on')->paginate(10, ['*'], 'attitude_page')->withQueryString();
        $achievementAssessments = AchievementAssessment::query()->where('student_user_id', $student->id)
            ->when($selectedClassId, fn ($query) => $query->where('school_class_id', $selectedClassId))
            ->with('subject')->latest('awarded_on')->paginate(10, ['*'], 'achievement_page')->withQueryString();
        $gradeAssessments = GradeAssessment::query()
            ->whereHas('grades', fn ($query) => $query->where('student_user_id', $student->id))
            ->when($selectedClassId, fn ($query) => $query->where('school_class_id', $selectedClassId))
            ->with([
                'subject',
                'semester.academicYear',
                'grades' => fn ($query) => $query->where('student_user_id', $student->id),
            ])
            ->latest('assessed_on')->latest('id')
            ->paginate(20, ['*'], 'grades_page')->withQueryString();
        $bills = $student->bills()->when($selectedClassId, fn ($query) => $query->where('school_class_id', $selectedClassId))
            ->with('payments')->latest()->paginate(10, ['*'], 'bills_page')->withQueryString();
        $classHistories = StudentClassHistory::query()->where('student_user_id', $student->id)
            ->whereIn('school_class_id', $availableClassIds)
            ->when($selectedClassId, fn ($query) => $query->where('school_class_id', $selectedClassId))
            ->with(['schoolClass', 'academicYear'])->latest('started_on')
            ->paginate(10, ['*'], 'history_page')->withQueryString();

        return view('students.show', [
            'student' => $student,
            'canManage' => $user->canDo('school.manage'),
            'classOptions' => $classOptions,
            'selectedClassId' => $selectedClassId,
            'attendanceMonth' => $attendanceMonth,
            'attendanceMonths' => $attendanceMonths,
            'attendanceCount' => $attendanceCount,
            'attendanceRate' => $attendanceCount ? (int) round($presentCount / $attendanceCount * 100) : null,
            'attendances' => $attendances,
            'transactions' => $transactions,
            'attitudeAssessments' => $attitudeAssessments,
            'achievementAssessments' => $achievementAssessments,
            'gradeAssessments' => $gradeAssessments,
            'gradeKinds' => GradeAssessment::KINDS,
            'bills' => $bills,
            'classHistories' => $classHistories,
            'parents' => DB::table('parent_student')->join('users', 'users.id', '=', 'parent_student.parent_user_id')
                ->leftJoin('parent_profiles', 'parent_profiles.user_id', '=', 'users.id')
                ->where('parent_student.student_user_id', $student->id)
                ->select('users.name', 'users.email', 'parent_profiles.phone', 'parent_student.relationship')->get(),
        ]);
    }

    public function classDirectory(Request $request, string $section = 'classes')
    {
        abort_unless(in_array($section, ['classes', 'subjects', 'teaching', 'promotions'], true), 404);
        $canManage = $request->user()->canDo('school.manage');

        return view('classes.index', [
            'section' => $section,
            'canManage' => $canManage,
            'classes' => SchoolClass::query()
                ->with(['academicYear', 'homeroomTeacher', 'students.user', 'teachingAssignments.teacher', 'teachingAssignments.subject'])
                ->when(! $canManage, fn ($query) => $query->where(function ($classes) use ($request) {
                    $classes->where('homeroom_teacher_id', $request->user()->id)
                        ->orWhereHas('teachingAssignments', fn ($assignments) => $assignments->where('teacher_user_id', $request->user()->id));
                }))
                ->orderBy('grade_level')
                ->orderBy('name')
                ->get(),
            'teachers' => User::query()->where('role', 'teacher')->where('is_active', true)->orderBy('name')->get(),
            'subjects' => Subject::query()->orderBy('name')->get(),
            'assignments' => TeachingAssignment::query()->with(['teacher', 'schoolClass', 'subject'])
                ->orderBy('school_class_id')->orderBy('teacher_user_id')->get(),
            'students' => $section === 'promotions'
                ? User::query()->where('role', 'student')->where('is_active', true)->with('studentProfile.schoolClass')->orderBy('name')->get()
                : collect(),
        ]);
    }

    public function showClass(Request $request, SchoolClass $schoolClass)
    {
        $user = $request->user();
        $canManage = $user->canDo('school.manage');
        abort_unless($canManage || $schoolClass->homeroom_teacher_id === $user->id
            || $schoolClass->teachingAssignments()->where('teacher_user_id', $user->id)->exists(), 403);

        return view('classes.show', [
            'schoolClass' => $schoolClass->load(['academicYear', 'homeroomTeacher', 'teachingAssignments.teacher', 'teachingAssignments.subject']),
            'canManage' => $canManage,
            'students' => User::query()->where('role', 'student')
                ->whereHas('studentProfile', fn ($query) => $query->where('school_class_id', $schoolClass->id))
                ->with(['studentProfile', 'bills'])
                ->orderBy('name')->get(),
            'todayAttendances' => Attendance::query()->where('school_class_id', $schoolClass->id)
                ->whereDate('attendance_date', today())->get()->keyBy('student_user_id'),
            'destinationClasses' => $canManage
                ? SchoolClass::query()->where('id', '!=', $schoolClass->id)->with('academicYear')->orderBy('grade_level')->orderBy('name')->get()
                : collect(),
        ]);
    }

    public function recordClassAttendance(Request $request, SchoolClass $schoolClass)
    {
        $user = $request->user();
        abort_unless($user->canDo('school.manage') || $schoolClass->homeroom_teacher_id === $user->id, 403);

        $data = $request->validate([
            'attendance' => ['required', 'array'],
            'attendance.*' => ['required', Rule::in(['present', 'sick', 'excused', 'absent'])],
        ]);
        $students = User::query()->where('role', 'student')
            ->whereHas('studentProfile', fn ($query) => $query->where('school_class_id', $schoolClass->id))
            ->get()->keyBy('id');
        if (collect(array_keys($data['attendance']))->diff($students->keys())->isNotEmpty()) {
            throw ValidationException::withMessages(['attendance' => 'Daftar siswa tidak sesuai dengan kelas ini. Muat ulang halaman.']);
        }

        $attendanceDate = today()->toDateString();
        $semesterId = Semester::query()->whereDate('starts_on', '<=', $attendanceDate)
            ->whereDate('ends_on', '>=', $attendanceDate)->value('id');
        DB::transaction(function () use ($data, $students, $schoolClass, $user, $attendanceDate, $semesterId) {
            foreach ($data['attendance'] as $studentId => $status) {
                $student = $students->get($studentId);
                if (! $student) {
                    continue;
                }
                $attendance = Attendance::query()->firstOrCreate([
                    'student_user_id' => $student->id,
                    'attendance_date' => $attendanceDate,
                ], [
                    'school_class_id' => $schoolClass->id,
                    'academic_year_id' => $schoolClass->academic_year_id,
                    'semester_id' => $semesterId,
                    'created_by_user_id' => $user->id,
                    'status' => $status,
                    'source' => 'homeroom',
                    'checked_in_at' => $status === 'present' ? now()->format('H:i:s') : null,
                    'is_ontime' => false,
                    'is_within_radius' => false,
                ]);
                if (! $attendance->wasRecentlyCreated && $attendance->source === 'homeroom') {
                    $attendance->update([
                        'school_class_id' => $schoolClass->id,
                        'academic_year_id' => $schoolClass->academic_year_id,
                        'semester_id' => $semesterId,
                        'status' => $status,
                        'checked_in_at' => $status === 'present' ? ($attendance->checked_in_at ?? now()->format('H:i:s')) : null,
                        'created_by_user_id' => $user->id,
                    ]);
                }
            }
        });

        return back()->with('status', 'Absensi kelas hari ini berhasil disimpan. Catatan GPS/fingerprint yang sudah ada tetap dipertahankan.');
    }

    public function promoteStudents(Request $request, SchoolClass $schoolClass, FinanceService $finance)
    {
        $data = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['required', 'integer', 'distinct', 'exists:users,id'],
            'destination_class_id' => ['required', 'integer', 'exists:school_classes,id'],
        ]);
        $students = User::query()->where('role', 'student')
            ->whereIn('id', $data['student_ids'])
            ->whereHas('studentProfile', fn ($query) => $query->where('school_class_id', $schoolClass->id))
            ->get();

        if ($students->count() !== count($data['student_ids'])) {
            throw ValidationException::withMessages(['student_ids' => 'Pastikan siswa yang dipilih berasal dari kelas ini.']);
        }

        $destination = SchoolClass::query()->findOrFail($data['destination_class_id']);
        if ($destination->id === $schoolClass->id) {
            throw ValidationException::withMessages(['destination_class_id' => 'Pilih kelas tujuan yang berbeda.']);
        }

        DB::transaction(function () use ($students, $destination, $finance) {
            foreach ($students as $student) {
                $finance->promoteStudent($student, $destination);
            }
        });

        return back()->with('status', $students->count().' siswa berhasil dipindahkan ke '.$destination->name.'. Tagihan yang belum lunas tetap tercatat sebagai tunggakan.');
    }

    public function storeSubject(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:30', 'unique:subjects,code'],
        ]);
        Subject::query()->create($data);

        return back()->with('status', 'Mata pelajaran ditambahkan.');
    }

    public function storeTeachingAssignment(Request $request)
    {
        $data = $request->validate([
            'teacher_user_id' => ['required', 'exists:users,id'],
            'school_class_ids' => ['required', 'array', 'min:1'],
            'school_class_ids.*' => ['required', 'integer', 'distinct', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
        ]);
        User::query()->where('role', 'teacher')->findOrFail($data['teacher_user_id']);
        foreach ($data['school_class_ids'] as $classId) {
            TeachingAssignment::query()->firstOrCreate([
                'teacher_user_id' => $data['teacher_user_id'],
                'school_class_id' => $classId,
                'subject_id' => $data['subject_id'],
            ]);
        }

        return back()->with('status', 'Guru mata pelajaran ditugaskan ke kelas.');
    }

    public function destroyTeachingAssignment(TeachingAssignment $teachingAssignment)
    {
        $teachingAssignment->delete();

        return back()->with('status', 'Penugasan mengajar dihapus.');
    }

    public function users(Request $request, string $section = 'accounts')
    {
        abort_unless(in_array($section, ['accounts', 'create', 'import', 'roles', 'parents'], true), 404);

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', 'string', 'exists:roles,name'],
            'per_page' => ['nullable', 'integer', Rule::in([20, 50, 100])],
        ]);
        $perPage = (int) ($filters['per_page'] ?? 20);

        return view('admin.users', [
            'section' => $section,
            'filters' => $filters,
            'users' => User::query()
                ->with(['studentProfile.schoolClass', 'roles'])
                ->when($filters['q'] ?? null, function ($query, $search) {
                    $query->where(function ($accounts) use ($search) {
                        $accounts->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                })
                ->when($filters['role'] ?? null, fn ($query, $role) => $query->where('role', $role))
                ->orderBy('name')
                ->paginate($perPage)
                ->withQueryString(),
            'classes' => SchoolClass::query()->orderBy('name')->get(),
            'parents' => User::query()->where('role', 'parent')->orderBy('name')->get(),
            'students' => User::query()->where('role', 'student')->orderBy('name')->get(),
            'roles' => Role::query()->with('permissions')->orderBy('label')->get(),
            'permissions' => Permission::query()->orderBy('label')->get(),
            'importDefaultPassword' => config('auth.import_default_password'),
        ]);
    }

    public function storeUser(Request $request)
    {
        if ($request->filled('username')) {
            $request->merge(['username' => Str::lower($request->string('username')->trim()->toString())]);
        }

        $roleName = Role::query()->whereKey($request->input('role_id'))->value('name');
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'required_without:email', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', 'unique:users,username'],
            'email' => ['nullable', 'required_without:username', 'email', 'unique:users,email'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'min:8'],
            'school_class_id' => [Rule::requiredIf($roleName === 'student'), 'nullable', 'exists:school_classes,id'],
            'nis' => [Rule::requiredIf($roleName === 'student'), 'nullable', 'string', 'max:50', 'unique:student_profiles,nis'],
            'employee_number' => [Rule::requiredIf($roleName === 'teacher'), 'nullable', 'string', 'max:50', 'unique:teacher_profiles,employee_number'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        DB::transaction(function () use ($data, $roleName) {
            $user = User::query()->create([
                'name' => $data['name'],
                'username' => $data['username'] ?? null,
                'email' => $data['email'] ?? null,
                'role' => $roleName,
                'password' => Hash::make($data['password']),
                'must_change_password' => true,
            ]);
            $user->roles()->attach($data['role_id']);

            match ($roleName) {
                'student' => StudentProfile::query()->create([
                    'user_id' => $user->id,
                    'school_class_id' => $data['school_class_id'] ?? null,
                    'nis' => $data['nis'] ?? null,
                ]),
                'teacher' => TeacherProfile::query()->create([
                    'user_id' => $user->id,
                    'employee_number' => $data['employee_number'] ?? null,
                ]),
                'parent' => ParentProfile::query()->create([
                    'user_id' => $user->id,
                    'phone' => $data['phone'] ?? null,
                ]),
                default => null,
            };

            if ($roleName === 'student') {
                $schoolClass = SchoolClass::query()->findOrFail($data['school_class_id']);
                StudentClassHistory::query()->create([
                    'student_user_id' => $user->id,
                    'school_class_id' => $schoolClass->id,
                    'academic_year_id' => $schoolClass->academic_year_id,
                    'started_on' => today(),
                ]);
            }
        });

        return back()->with('status', 'Akun berhasil dibuat.');
    }

    public function storeRole(Request $request)
    {
        $request->merge(['name' => Str::slug((string) $request->input('name'), '_')]);
        $data = $request->validate([
            'name' => ['required', 'alpha_dash', 'max:20', 'unique:roles,name'],
            'label' => ['required', 'string', 'max:100'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role = Role::query()->create(['name' => $data['name'], 'label' => $data['label']]);
        $role->permissions()->sync($data['permission_ids'] ?? []);

        return back()->with('status', 'Role dan permission berhasil dibuat.');
    }

    public function assignRole(Request $request, User $user)
    {
        $data = $request->validate(['role_id' => ['required', 'exists:roles,id']]);
        $role = Role::query()->findOrFail($data['role_id']);

        if ($user->hasRole('superadmin') && $role->name !== 'superadmin'
            && User::query()->where('role', 'superadmin')->where('is_active', true)->count() <= 1) {
            throw ValidationException::withMessages(['role_id' => 'Superadmin aktif terakhir tidak boleh diturunkan rolenya.']);
        }

        DB::transaction(function () use ($user, $role) {
            $user->roles()->sync([$role->id]);
            $user->update(['role' => $role->name]);
        });

        return back()->with('status', 'Role akun berhasil diperbarui.');
    }

    public function updateRole(Request $request, Role $role)
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        DB::transaction(function () use ($role, $data) {
            $role->update(['label' => $data['label']]);
            $role->permissions()->sync($data['permission_ids'] ?? []);
        });

        return back()->with('status', 'Permission role berhasil diperbarui.');
    }

    public function importGoogleSheet(Request $request, GoogleSheetAccountImportService $importer)
    {
        $data = $request->validate([
            'sheet_url' => ['required', 'url:https'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);
        $result = $importer->import($data['sheet_url'], Role::query()->findOrFail($data['role_id']));

        return back()
            ->with('status', "Import selesai: {$result['created']} akun dibuat, {$result['skipped']} baris dilewati.")
            ->with('import_errors', $result['errors']);
    }

    public function importSpreadsheet(Request $request, GoogleSheetAccountImportService $importer)
    {
        $data = $request->validate([
            'spreadsheet' => ['required', 'file', 'mimes:csv,txt,xlsx', 'max:5120'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);
        $result = $importer->importFile($request->file('spreadsheet'), Role::query()->findOrFail($data['role_id']));

        return back()
            ->with('status', "Import selesai: {$result['created']} akun dibuat, {$result['skipped']} baris dilewati.")
            ->with('import_errors', $result['errors']);
    }

    public function downloadImportTemplate(Role $role, GoogleSheetAccountImportService $importer)
    {
        $filename = 'template-akun-'.$role->name.'.xlsx';

        return response($importer->templateContents($role), 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function linkParent(Request $request)
    {
        $data = $request->validate([
            'parent_user_id' => ['required', 'exists:users,id'],
            'student_user_id' => ['required', 'exists:users,id'],
            'relationship' => ['required', 'string', 'max:50'],
        ]);

        $parent = User::query()->where('role', 'parent')->findOrFail($data['parent_user_id']);
        User::query()->where('role', 'student')->findOrFail($data['student_user_id']);

        $parent->children()->syncWithoutDetaching([
            $data['student_user_id'] => ['relationship' => $data['relationship']],
        ]);

        return back()->with('status', 'Orang tua berhasil dihubungkan ke siswa.');
    }

    public function settings(string $section = 'general')
    {
        abort_unless(in_array($section, ['general', 'classes', 'academic', 'iot'], true), 404);

        return view('admin.settings', [
            'section' => $section,
            'setting' => SchoolSetting::active(),
            'classes' => SchoolClass::query()->latest()->get(),
            'teachers' => User::query()->where('role', 'teacher')->where('is_active', true)->orderBy('name')->get(),
            'devices' => IotDevice::query()->latest()->get(),
            'academicYears' => AcademicYear::query()->orderByDesc('starts_on')->get(),
            'semesters' => Semester::query()->with('academicYear')->orderByDesc('starts_on')->get(),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
            'attendance_radius_meters' => ['required', 'integer', 'min:10', 'max:5000'],
            'max_location_accuracy_meters' => ['required', 'integer', 'min:5', 'max:1000'],
            'attendance_open_time' => ['required', 'date_format:H:i'],
            'start_time' => ['required', 'date_format:H:i', 'after_or_equal:attendance_open_time'],
            'late_after' => ['required', 'date_format:H:i', 'after_or_equal:start_time'],
            'attendance_close_time' => ['required', 'date_format:H:i', 'after_or_equal:late_after'],
        ]);

        SchoolSetting::active()->update($data);

        return back()->with('status', 'Pengaturan sekolah disimpan.');
    }

    public function storeClass(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'grade_level' => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        $data['academic_year_id'] = AcademicYear::query()->where('is_active', true)->value('id');
        SchoolClass::query()->create($data);

        return back()->with('status', 'Kelas berhasil dibuat.');
    }

    public function storeAcademicYear(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:academic_years,name'],
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after:starts_on'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $isActive = $request->boolean('is_active');

        DB::transaction(function () use ($data, $isActive) {
            if ($isActive) {
                AcademicYear::query()->update(['is_active' => false]);
            }

            AcademicYear::query()->create([...$data, 'is_active' => $isActive]);
        });

        return back()->with('status', 'Tahun ajaran berhasil dibuat.');
    }

    public function storeSemester(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name' => [
                'required', 'string', 'max:50',
                Rule::unique('semesters', 'name')->where('academic_year_id', $request->input('academic_year_id')),
            ],
            'starts_on' => ['required', 'date'],
            'ends_on' => ['required', 'date', 'after:starts_on'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $academicYear = AcademicYear::query()->findOrFail($data['academic_year_id']);

        if ($data['starts_on'] < $academicYear->starts_on->toDateString()
            || $data['ends_on'] > $academicYear->ends_on->toDateString()) {
            throw ValidationException::withMessages([
                'starts_on' => 'Rentang semester harus berada di dalam tahun ajaran.',
            ]);
        }

        $overlaps = Semester::query()
            ->where('academic_year_id', $academicYear->id)
            ->whereDate('starts_on', '<=', $data['ends_on'])
            ->whereDate('ends_on', '>=', $data['starts_on'])
            ->exists();

        if ($overlaps) {
            throw ValidationException::withMessages([
                'starts_on' => 'Rentang semester bertumpang tindih dengan semester yang sudah ada.',
            ]);
        }

        $isActive = $request->boolean('is_active');
        DB::transaction(function () use ($data, $isActive) {
            if ($isActive) {
                Semester::query()->update(['is_active' => false]);
            }

            Semester::query()->create([...$data, 'is_active' => $isActive]);
        });

        return back()->with('status', 'Semester berhasil dibuat.');
    }

    public function storeDevice(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'identifier' => ['required', 'string', 'max:100', 'unique:iot_devices,identifier'],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
        ]);

        $plainToken = Str::random(40);

        IotDevice::query()->create([
            ...$data,
            'api_token_hash' => Hash::make($plainToken),
        ]);

        return back()->with('device_token', $plainToken)->with('status', 'Perangkat IoT berhasil dibuat. Simpan token ini di firmware/perangkat.');
    }

    public function setHomeroomTeacher(Request $request, SchoolClass $schoolClass)
    {
        $data = $request->validate([
            'homeroom_teacher_id' => ['nullable', 'exists:users,id'],
        ]);

        if (! empty($data['homeroom_teacher_id'])) {
            User::query()->where('role', 'teacher')->findOrFail($data['homeroom_teacher_id']);
        }

        $schoolClass->update($data);

        return back()->with('status', 'Wali kelas berhasil diperbarui.');
    }
}
