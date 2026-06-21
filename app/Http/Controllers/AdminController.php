<?php

namespace App\Http\Controllers;

use App\Models\IotDevice;
use App\Models\ParentProfile;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function users()
    {
        return view('admin.users', [
            'users' => User::query()->with('studentProfile.schoolClass')->latest()->paginate(20),
            'classes' => SchoolClass::query()->orderBy('name')->get(),
            'parents' => User::query()->where('role', 'parent')->orderBy('name')->get(),
            'students' => User::query()->where('role', 'student')->orderBy('name')->get(),
        ]);
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'in:admin,teacher,student,parent'],
            'password' => ['required', 'min:8'],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'nis' => ['nullable', 'string', 'max:50'],
            'employee_number' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        DB::transaction(function () use ($data) {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'],
                'password' => Hash::make($data['password']),
            ]);

            match ($data['role']) {
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
        });

        return back()->with('status', 'Akun berhasil dibuat.');
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

    public function settings()
    {
        return view('admin.settings', [
            'setting' => SchoolSetting::active(),
            'classes' => SchoolClass::query()->latest()->get(),
            'devices' => IotDevice::query()->latest()->get(),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'attendance_radius_meters' => ['required', 'integer', 'min:10', 'max:5000'],
            'start_time' => ['required', 'date_format:H:i'],
            'late_after' => ['required', 'date_format:H:i'],
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

        SchoolClass::query()->create($data);

        return back()->with('status', 'Kelas berhasil dibuat.');
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
}
