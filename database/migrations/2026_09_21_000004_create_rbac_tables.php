<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('label');
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'user_id']);
        });

        $now = now();
        $roles = [
            ['name' => 'superadmin', 'label' => 'Superadmin', 'is_system' => true],
            ['name' => 'tu', 'label' => 'Tata Usaha', 'is_system' => true],
            ['name' => 'teacher', 'label' => 'Guru', 'is_system' => true],
            ['name' => 'student', 'label' => 'Siswa', 'is_system' => true],
            ['name' => 'parent', 'label' => 'Orang Tua', 'is_system' => true],
        ];
        $permissions = [
            ['name' => 'users.manage', 'label' => 'Kelola akun dan RBAC'],
            ['name' => 'school.manage', 'label' => 'Kelola data sekolah'],
            ['name' => 'finance.manage', 'label' => 'Kelola tagihan dan pembayaran'],
            ['name' => 'finance.propose', 'label' => 'Mengusulkan biaya kelas'],
            ['name' => 'attendance.manage', 'label' => 'Melihat dan mengelola absensi'],
            ['name' => 'attendance.checkin', 'label' => 'Melakukan absensi'],
            ['name' => 'assessments.manage', 'label' => 'Memberikan penilaian'],
            ['name' => 'reports.export', 'label' => 'Mengunduh laporan'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([...$role, 'created_at' => $now, 'updated_at' => $now]);
        }
        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([...$permission, 'created_at' => $now, 'updated_at' => $now]);
        }

        $roleIds = DB::table('roles')->pluck('id', 'name');
        $permissionIds = DB::table('permissions')->pluck('id', 'name');
        $grants = [
            'superadmin' => array_keys($permissionIds->all()),
            'tu' => ['finance.manage'],
            'teacher' => ['finance.propose', 'attendance.manage', 'assessments.manage', 'reports.export'],
            'student' => ['attendance.checkin'],
            'parent' => [],
        ];

        foreach ($grants as $roleName => $permissionNames) {
            foreach ($permissionNames as $permissionName) {
                DB::table('permission_role')->insert([
                    'role_id' => $roleIds[$roleName],
                    'permission_id' => $permissionIds[$permissionName],
                ]);
            }
        }

        DB::table('users')->where('role', 'admin')->update(['role' => 'superadmin']);
        DB::table('users')->orderBy('id')->each(function ($user) use ($roleIds) {
            if (isset($roleIds[$user->role])) {
                DB::table('role_user')->insertOrIgnore([
                    'role_id' => $roleIds[$user->role],
                    'user_id' => $user->id,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');

        DB::table('users')->where('role', 'superadmin')->update(['role' => 'admin']);
    }
};
