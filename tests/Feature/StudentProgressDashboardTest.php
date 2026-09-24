<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\PointTransaction;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Semester;
use App\Models\StudentClassHistory;
use App\Models\StudentPointSummary;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StudentProgressDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_and_parent_see_gamified_progress_without_financial_amounts_on_dashboard(): void
    {
        [$student, $parent, $semester] = $this->progressFixture();

        foreach ([$student, $parent] as $user) {
            $this->actingAs($user)->get(route('dashboard', ['semester' => $semester->id]))
                ->assertOk()
                ->assertSee('Profil perkembangan')
                ->assertSee('X IPA 1')
                ->assertSee('Total check-in')
                ->assertSee('Hadir tepat waktu')
                ->assertSee('Aktivitas Terbaru')
                ->assertSee('Riwayat Kelas')
                ->assertSee('Keuangan')
                ->assertDontSee('Daftar Tagihan')
                ->assertDontSee('Rp ');
        }

        $this->actingAs($student)->get(route('dashboard', ['semester' => $semester->id]))
            ->assertSee('Radius sekolah 100 m')
            ->assertSee('name="accuracy"', false)
            ->assertSee('name="location_timestamp"', false);
    }

    public function test_semester_filter_only_counts_attendance_in_selected_semester(): void
    {
        [$student, , $semester, $otherSemester, $class] = $this->progressFixture();
        Attendance::query()->create([
            'student_user_id' => $student->id,
            'school_class_id' => $class->id,
            'academic_year_id' => $class->academic_year_id,
            'semester_id' => $otherSemester->id,
            'attendance_date' => '2027-01-10',
            'checked_in_at' => '07:10:00',
            'status' => 'late',
            'source' => 'web',
            'is_within_radius' => true,
            'is_ontime' => false,
        ]);

        $this->actingAs($student)->get(route('dashboard', ['semester' => $semester->id]))
            ->assertOk()
            ->assertSeeInOrder(['1', 'Total check-in'])
            ->assertDontSee('Hadir terlambat');
    }

    public function test_superadmin_dashboard_shows_podium_and_can_filter_by_class(): void
    {
        [$student, , , , $class] = $this->progressFixture();
        $admin = User::query()->create([
            'name' => 'Admin', 'email' => 'podium-admin@test.test', 'role' => 'superadmin',
            'password' => 'password', 'must_change_password' => false, 'is_active' => true,
        ]);

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()->assertSee('Podium siswa rajin')->assertSee('Raka')->assertSee('65 poin');
        $this->get(route('dashboard', ['podium_class' => $class->id]))
            ->assertOk()->assertSee('Podium siswa rajin')->assertSee('Raka');

        $teacher = User::query()->create([
            'name' => 'Guru', 'email' => 'podium-teacher@test.test', 'role' => 'teacher',
            'password' => 'password', 'must_change_password' => false, 'is_active' => true,
        ]);
        $this->actingAs($teacher)->get(route('dashboard'))
            ->assertOk()->assertDontSee('Podium siswa rajin');
    }

    private function progressFixture(): array
    {
        $this->travelTo(Carbon::parse('2026-09-22 06:30:00'));
        SchoolSetting::active()->update(['latitude' => -6.2, 'longitude' => 106.8]);
        $year = AcademicYear::query()->create([
            'name' => '2026/2027', 'starts_on' => '2026-07-01', 'ends_on' => '2027-06-30', 'is_active' => true,
        ]);
        $semester = Semester::query()->create([
            'academic_year_id' => $year->id, 'name' => 'Ganjil', 'starts_on' => '2026-07-01', 'ends_on' => '2026-12-31', 'is_active' => true,
        ]);
        $otherSemester = Semester::query()->create([
            'academic_year_id' => $year->id, 'name' => 'Genap', 'starts_on' => '2027-01-01', 'ends_on' => '2027-06-30',
        ]);
        $class = SchoolClass::query()->create(['academic_year_id' => $year->id, 'name' => 'X IPA 1', 'grade_level' => 10]);
        $student = User::query()->create([
            'name' => 'Raka', 'email' => 'raka@test.test', 'role' => 'student', 'password' => 'password', 'is_active' => true,
        ]);
        StudentProfile::query()->create(['user_id' => $student->id, 'school_class_id' => $class->id, 'nis' => '1001']);
        StudentClassHistory::query()->create([
            'student_user_id' => $student->id, 'school_class_id' => $class->id, 'academic_year_id' => $year->id, 'started_on' => '2026-07-01',
        ]);
        StudentPointSummary::query()->create([
            'student_user_id' => $student->id, 'general_points' => 65, 'attitude_points' => 20,
            'attendance_points' => 25, 'achievement_points' => 20, 'label' => 'Berkembang Baik',
        ]);
        $attendance = Attendance::query()->create([
            'student_user_id' => $student->id,
            'school_class_id' => $class->id,
            'academic_year_id' => $year->id,
            'semester_id' => $semester->id,
            'attendance_date' => '2026-09-21',
            'checked_in_at' => '06:45:00',
            'status' => 'present',
            'source' => 'fingerprint',
            'is_within_radius' => true,
            'is_ontime' => true,
        ]);
        PointTransaction::query()->create([
            'student_user_id' => $student->id, 'type' => 'achievement', 'points' => 20,
            'source_type' => Attendance::class, 'source_id' => $attendance->id, 'description' => 'Juara lomba kelas',
        ]);
        $parent = User::query()->create([
            'name' => 'Orang Tua Raka', 'email' => 'parent-raka@test.test', 'role' => 'parent', 'password' => 'password', 'is_active' => true,
        ]);
        $parent->children()->attach($student->id, ['relationship' => 'parent']);

        return [$student, $parent, $semester, $otherSemester, $class];
    }
}
