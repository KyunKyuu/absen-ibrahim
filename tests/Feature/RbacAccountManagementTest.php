<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RbacAccountManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_modules_are_split_into_focused_submenu_pages(): void
    {
        $superadmin = $this->superadmin();
        $this->actingAs($superadmin);

        $pages = [
            [route('admin.users.create'), 'Buat akun'],
            [route('admin.users.import'), 'Import akun'],
            [route('admin.users.roles'), 'Role &amp; akses'],
            [route('admin.users.parents'), 'Relasi orang tua'],
            [route('admin.settings.classes'), 'Kelas &amp; wali kelas'],
            [route('admin.settings.academic'), 'Tahun ajaran &amp; semester'],
            [route('admin.settings.iot'), 'Perangkat IoT'],
            [route('teacher.assessments.achievements'), 'Prestasi &amp; pelanggaran'],
            [route('attendance.today'), 'Absensi hari ini'],
            [route('finance.issue'), 'Terbitkan tagihan'],
            [route('finance.fee-types'), 'Jenis tagihan'],
            [route('finance.proposals'), 'Usulan biaya'],
            [route('classes.promotions'), 'Kenaikan kelas'],
        ];

        foreach ($pages as [$url, $heading]) {
            $this->get($url)->assertOk()->assertSee($heading, false);
        }
        $this->get(route('finance.promotions'))->assertRedirect(route('classes.promotions'));
    }

    public function test_account_list_supports_search_role_filter_and_pagination(): void
    {
        $superadmin = $this->superadmin();
        foreach (range(1, 21) as $index) {
            User::query()->create([
                'name' => 'Siswa '.str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                'username' => 'siswa'.$index,
                'role' => 'student',
                'password' => 'password',
                'is_active' => true,
            ]);
        }
        User::query()->create(['name' => 'Guru Khusus', 'username' => 'guru.khusus', 'role' => 'teacher', 'password' => 'password', 'is_active' => true]);

        $this->actingAs($superadmin)->get(route('admin.users', ['q' => 'Guru Khusus', 'role' => 'teacher']))
            ->assertOk()
            ->assertSee('Guru Khusus')
            ->assertDontSee('Siswa 01');

        $this->actingAs($superadmin)->get(route('admin.users', ['role' => 'student']))
            ->assertOk()
            ->assertSee('Halaman 1 dari 2');

        $this->actingAs($superadmin)->get(route('admin.users.create'))
            ->assertOk()
            ->assertSee('data-role-fields="student"', false)
            ->assertSee('data-role-fields="teacher"', false);
    }

    public function test_superadmin_can_create_custom_role_and_account_with_username(): void
    {
        $superadmin = $this->superadmin();
        $permission = Permission::query()->where('name', 'attendance.manage')->firstOrFail();

        $this->actingAs($superadmin)->post(route('admin.roles.store'), [
            'name' => 'operator absensi',
            'label' => 'Operator Absensi',
            'permission_ids' => [$permission->id],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $role = Role::query()->where('name', 'operator_absensi')->firstOrFail();
        $this->actingAs($superadmin)->post(route('admin.users.store'), [
            'name' => 'Operator Baru',
            'username' => 'operator.baru',
            'role_id' => $role->id,
            'password' => 'password123',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $account = User::query()->where('username', 'operator.baru')->firstOrFail();
        $this->assertTrue($account->hasRole('operator_absensi'));
        $this->assertTrue($account->canDo('attendance.manage'));
        $this->assertTrue($account->must_change_password);
        $this->actingAs($account)->get(route('attendance.index'))->assertRedirect(route('password.change'));

        $reportPermission = Permission::query()->where('name', 'reports.export')->firstOrFail();
        $this->actingAs($superadmin)->post(route('admin.roles.update', $role), [
            'label' => 'Operator Laporan',
            'permission_ids' => [$reportPermission->id],
        ])->assertSessionHasNoErrors();
        $this->assertTrue($account->canDo('reports.export'));
        $this->assertFalse($account->canDo('attendance.manage'));
        $this->actingAs($superadmin)->get(route('admin.users.import'))
            ->assertOk()
            ->assertSee('Import akun');
        $this->actingAs($superadmin)->get(route('admin.users.roles'))
            ->assertOk()
            ->assertSee('Buat role baru');
    }

    public function test_superadmin_can_create_another_superadmin(): void
    {
        $superadmin = $this->superadmin();
        $role = Role::query()->where('name', 'superadmin')->firstOrFail();

        $this->actingAs($superadmin)->post(route('admin.users.store'), [
            'name' => 'Superadmin Kedua',
            'username' => 'superadmin.dua',
            'role_id' => $role->id,
            'password' => 'password123',
        ])->assertSessionHasNoErrors();

        $newAccount = User::query()->where('username', 'superadmin.dua')->firstOrFail();
        $this->assertTrue($newAccount->hasRole('superadmin'));
        $this->assertTrue($newAccount->must_change_password);
    }

    public function test_google_sheet_import_creates_student_username_and_default_password(): void
    {
        $superadmin = $this->superadmin();
        $year = AcademicYear::query()->create(['name' => '2026/2027', 'is_active' => true]);
        SchoolClass::query()->create(['name' => 'X IPA 1', 'academic_year_id' => $year->id]);
        $studentRole = Role::query()->where('name', 'student')->firstOrFail();
        Http::fake([
            'https://docs.google.com/*' => Http::response("nama,username,nis,kelas,email\nRaka Pramudya,raka.p,1001,X IPA 1,\nSiti Aminah,,1002,X IPA 1,siti@example.test\n"),
        ]);

        $this->actingAs($superadmin)->post(route('admin.users.import-google-sheet'), [
            'sheet_url' => 'https://docs.google.com/spreadsheets/d/abc123/edit#gid=42',
            'role_id' => $studentRole->id,
        ])->assertRedirect()->assertSessionHasNoErrors()->assertSessionHas('status', 'Import selesai: 2 akun dibuat, 0 baris dilewati.');

        $raka = User::query()->where('username', 'raka.p')->firstOrFail();
        $siti = User::query()->where('username', 'siti.aminah')->firstOrFail();
        $this->assertTrue(Hash::check(config('auth.import_default_password'), $raka->password));
        $this->assertTrue($raka->must_change_password);
        $this->assertTrue($siti->hasRole('student'));
        $this->assertDatabaseHas('student_profiles', ['user_id' => $raka->id, 'nis' => '1001']);
        $this->assertDatabaseCount('student_class_histories', 2);

        Http::assertSent(fn ($request) => $request->url() === 'https://docs.google.com/spreadsheets/d/abc123/export?format=csv&gid=42');
    }

    public function test_excel_import_creates_student_profile_and_class_history(): void
    {
        $superadmin = $this->superadmin();
        $year = AcademicYear::query()->create(['name' => '2027/2028', 'is_active' => true]);
        $class = SchoolClass::query()->create(['name' => '10A', 'academic_year_id' => $year->id]);
        $studentRole = Role::query()->where('name', 'student')->firstOrFail();
        $path = tempnam(sys_get_temp_dir(), 'students-xlsx-');
        $zip = new \ZipArchive;
        $zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData><row r="1"><c r="A1" t="inlineStr"><is><t>nama</t></is></c><c r="B1" t="inlineStr"><is><t>nis</t></is></c><c r="C1" t="inlineStr"><is><t>kelas</t></is></c></row><row r="2"><c r="A2" t="inlineStr"><is><t>Siswa Excel</t></is></c><c r="B2" t="inlineStr"><is><t>EX-001</t></is></c><c r="C2" t="inlineStr"><is><t>10A</t></is></c></row></sheetData></worksheet>');
        $zip->close();

        $file = new UploadedFile($path, 'siswa.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
        $this->actingAs($superadmin)->post(route('admin.users.import-spreadsheet'), [
            'spreadsheet' => $file,
            'role_id' => $studentRole->id,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $student = User::query()->where('name', 'Siswa Excel')->firstOrFail();
        $this->assertDatabaseHas('student_profiles', ['user_id' => $student->id, 'school_class_id' => $class->id, 'nis' => 'EX-001']);
        $this->assertDatabaseHas('student_class_histories', ['student_user_id' => $student->id, 'school_class_id' => $class->id]);
    }

    public function test_last_superadmin_cannot_demote_themselves(): void
    {
        $superadmin = $this->superadmin();
        $teacherRole = Role::query()->where('name', 'teacher')->firstOrFail();

        $this->actingAs($superadmin)->post(route('admin.users.role', $superadmin), [
            'role_id' => $teacherRole->id,
        ])->assertSessionHasErrors('role_id');

        $this->assertSame('superadmin', $superadmin->fresh()->role);
    }

    public function test_username_login_forces_imported_account_to_change_password(): void
    {
        $user = User::query()->create([
            'name' => 'Akun Import',
            'username' => 'akun.import',
            'role' => 'parent',
            'password' => 'password123',
            'is_active' => true,
            'must_change_password' => true,
        ]);

        $this->post('/login', ['login' => 'akun.import', 'password' => 'password123'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->get(route('dashboard'))->assertRedirect(route('password.change'));

        $this->post(route('password.update'), [
            'current_password' => 'password123',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ])->assertRedirect(route('dashboard'));

        $this->assertFalse($user->fresh()->must_change_password);
    }

    private function superadmin(): User
    {
        return User::query()->create([
            'name' => 'Superadmin',
            'username' => 'superadmin',
            'email' => 'superadmin@test.test',
            'role' => 'superadmin',
            'password' => 'password',
            'is_active' => true,
            'must_change_password' => false,
        ]);
    }
}
