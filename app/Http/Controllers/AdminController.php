<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\IotDevice;
use App\Models\ParentProfile;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Semester;
use App\Models\StudentClassHistory;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\TeachingAssignment;
use App\Models\Subject;
use App\Models\User;
use App\Services\GoogleSheetAccountImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function classDirectory(Request $request)
    {
        $canManage = $request->user()->canDo('school.manage');

        return view('classes.index', [
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
        ]);
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
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
        ]);
        User::query()->where('role', 'teacher')->findOrFail($data['teacher_user_id']);
        TeachingAssignment::query()->firstOrCreate($data);

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
