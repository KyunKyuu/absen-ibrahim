<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\FinanceProposal;
use App\Models\SchoolClass;
use App\Models\SchoolFeeType;
use App\Models\StudentBill;
use App\Models\StudentClassHistory;
use App\Models\PointTransaction;
use App\Models\StudentPointSummary;
use App\Models\StudentPayment;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FinanceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_payment_confirmation_waits_for_tu_verification(): void
    {
        Storage::fake('local');
        [, $tu, $class, $students] = $this->financeFixture();
        $student = $students->first();
        $bill = StudentBill::query()->create([
            'student_user_id' => $student->id,
            'school_class_id' => $class->id,
            'academic_year_id' => $class->academic_year_id,
            'created_by_user_id' => $tu->id,
            'title' => 'SPP September',
            'amount' => 250000,
        ]);

        $this->actingAs($student)->post(route('finance.payments.confirm', $bill), [
            'amount' => 250000,
            'paid_on' => '2026-09-21',
            'payment_method' => 'transfer',
            'reference' => 'TRX-001',
            'proof' => UploadedFile::fake()->image('bukti.jpg'),
        ])->assertRedirect(route('finance.payment-history'))->assertSessionHasNoErrors();

        $payment = StudentPayment::query()->sole();
        $this->assertSame('pending', $payment->status);
        $this->assertSame(0, $bill->fresh()->paid_amount);
        Storage::disk('local')->assertExists($payment->proof_path);

        $this->actingAs($tu)->post(route('finance.payments.approve', $payment))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertSame('verified', $payment->fresh()->status);
        $this->assertSame(250000, $bill->fresh()->paid_amount);
        $this->assertSame('paid', $bill->fresh()->status);
    }

    public function test_collective_fee_requires_tu_approval_and_preserves_the_exact_total(): void
    {
        [$teacher, $tu, $class, $students] = $this->financeFixture(3);

        $this->actingAs($teacher)->post(route('finance.proposals.store'), [
            'school_class_id' => $class->id,
            'title' => 'Camping kelas',
            'billing_mode' => 'collective',
            'amount' => 100000,
            'due_date' => '2026-10-20',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $proposal = FinanceProposal::query()->sole();
        $this->assertSame('pending', $proposal->status);
        $this->assertDatabaseCount('student_bills', 0);

        $this->actingAs($tu)->post(route('finance.proposals.approve', $proposal))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $bills = StudentBill::query()->orderBy('student_user_id')->get();
        $this->assertCount(3, $bills);
        $this->assertSame(100000, $bills->sum('amount'));
        $this->assertSame([33334, 33333, 33333], $bills->pluck('amount')->all());
        $this->assertSame('published', $proposal->fresh()->status);
        $this->assertEqualsCanonicalizing($students->pluck('id')->all(), $bills->pluck('student_user_id')->all());
    }

    public function test_tu_can_record_installments_but_cannot_overpay(): void
    {
        [, $tu, $class, $students] = $this->financeFixture();
        $bill = StudentBill::query()->create([
            'student_user_id' => $students->first()->id,
            'school_class_id' => $class->id,
            'academic_year_id' => $class->academic_year_id,
            'created_by_user_id' => $tu->id,
            'title' => 'SPP September',
            'amount' => 250000,
        ]);

        $this->actingAs($tu)->post(route('finance.payments.store', $bill), [
            'amount' => 100000,
            'paid_on' => '2026-09-21',
            'payment_method' => 'cash',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('student_bills', [
            'id' => $bill->id,
            'paid_amount' => 100000,
            'status' => 'partial',
        ]);

        $this->actingAs($tu)->post(route('finance.payments.store', $bill), [
            'amount' => 150001,
            'paid_on' => '2026-09-21',
            'payment_method' => 'cash',
        ])->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('student_payments', 1);
        $this->assertSame(100000, $bill->fresh()->paid_amount);

        $this->actingAs($tu)->post(route('finance.payments.store', $bill), [
            'amount' => 150000,
            'paid_on' => '2026-09-21',
            'payment_method' => 'transfer',
        ])->assertSessionHasNoErrors();

        $this->assertSame('paid', $bill->fresh()->status);
    }

    public function test_promotion_keeps_unpaid_bill_once_and_marks_it_as_arrears(): void
    {
        [, $tu, $oldClass, $students, $year] = $this->financeFixture();
        $admin = User::query()->create(['name' => 'Admin', 'email' => 'admin@test.test', 'role' => 'superadmin', 'password' => 'password', 'is_active' => true]);
        $newClass = SchoolClass::query()->create(['name' => 'XI A', 'grade_level' => 11, 'academic_year_id' => $year->id]);
        $student = $students->first();
        $bill = StudentBill::query()->create([
            'student_user_id' => $student->id,
            'school_class_id' => $oldClass->id,
            'academic_year_id' => $year->id,
            'created_by_user_id' => $tu->id,
            'title' => 'Uang Bangunan',
            'amount' => 1000000,
            'paid_amount' => 250000,
            'status' => 'partial',
        ]);

        $this->actingAs($admin)->post(route('classes.students.promote', $student), [
            'school_class_id' => $newClass->id,
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame($newClass->id, $student->studentProfile->fresh()->school_class_id);
        $this->assertTrue($bill->fresh()->is_arrears);
        $this->assertSame(750000, $bill->outstanding_amount);
        $this->assertDatabaseCount('student_bills', 1);
        $this->assertSame(2, StudentClassHistory::query()->where('student_user_id', $student->id)->count());
    }

    public function test_standard_class_billing_is_idempotent_for_the_same_period(): void
    {
        [, $tu, $class] = $this->financeFixture(2);
        $type = SchoolFeeType::query()->create([
            'name' => 'SPP', 'code' => 'SPP', 'billing_cycle' => 'monthly', 'default_amount' => 250000,
        ]);
        $payload = [
            'school_fee_type_id' => $type->id,
            'school_class_id' => $class->id,
            'billing_period' => '2026-09',
            'amount' => 250000,
            'due_date' => '2026-09-10',
        ];

        $this->actingAs($tu)->post(route('finance.bills.issue'), $payload)->assertSessionHasNoErrors();
        $this->actingAs($tu)->post(route('finance.bills.issue'), $payload)->assertSessionHasNoErrors();

        $this->assertDatabaseCount('student_bills', 2);
    }

    public function test_non_homeroom_teacher_cannot_propose_a_fee_for_another_class(): void
    {
        [, , $class] = $this->financeFixture();
        $otherTeacher = User::query()->create(['name' => 'Guru Lain', 'email' => 'other@test.test', 'role' => 'teacher', 'password' => 'password', 'is_active' => true]);

        $this->actingAs($otherTeacher)->post(route('finance.proposals.store'), [
            'school_class_id' => $class->id,
            'title' => 'Tidak sah',
            'billing_mode' => 'per_student',
            'amount' => 10000,
        ])->assertNotFound();

        $this->assertDatabaseCount('finance_proposals', 0);
    }

    public function test_finance_page_is_available_according_to_each_users_scope(): void
    {
        [$teacher, $tu, , $students] = $this->financeFixture();
        $parent = User::query()->create([
            'name' => 'Orang Tua', 'email' => 'parent@test.test', 'role' => 'parent', 'password' => 'password', 'is_active' => true,
        ]);
        $parent->children()->attach($students->first()->id, ['relationship' => 'parent']);

        $this->actingAs($tu)->get(route('finance.index'))->assertOk()->assertSee('Daftar tagihan');
        $this->actingAs($teacher)->get(route('finance.index'))->assertOk()->assertSee('Ajukan biaya kelas')->assertDontSee('Daftar tagihan');
        $this->actingAs($students->first())->get(route('finance.index'))->assertOk()->assertSee('Tagihan saya');
        $this->actingAs($parent)->get(route('finance.index'))->assertOk()->assertSee('Tagihan anak');
    }

    public function test_promoting_student_resets_current_points_and_starts_new_class_point_period(): void
    {
        [$teacher, , $oldClass, $students, $year] = $this->financeFixture();
        $student = $students->first();
        $newClass = SchoolClass::query()->create(['name' => 'XI A', 'grade_level' => 11, 'academic_year_id' => $year->id]);
        PointTransaction::query()->create([
            'student_user_id' => $student->id, 'type' => 'achievement', 'points' => 45, 'description' => 'Poin lama',
        ]);
        StudentPointSummary::query()->create([
            'student_user_id' => $student->id, 'general_points' => 45, 'achievement_points' => 45, 'label' => 'Perlu Dipantau',
        ]);
        StudentClassHistory::query()->create([
            'student_user_id' => $student->id, 'school_class_id' => $oldClass->id,
            'academic_year_id' => $year->id, 'started_on' => '2026-07-01',
        ]);

        app(\App\Services\FinanceService::class)->promoteStudent($student, $newClass);

        $this->assertDatabaseHas('student_point_summaries', [
            'student_user_id' => $student->id, 'general_points' => 0, 'achievement_points' => 0,
        ]);
        $this->assertDatabaseHas('point_transactions', [
            'student_user_id' => $student->id, 'type' => 'reset', 'points' => 0,
        ]);
        $this->assertDatabaseHas('student_class_histories', [
            'student_user_id' => $student->id, 'school_class_id' => $newClass->id,
        ]);

        app(\App\Services\PointCalculationService::class)->record($student, 'achievement', 10, 'Poin baru', $teacher);
        $this->assertDatabaseHas('student_point_summaries', [
            'student_user_id' => $student->id, 'general_points' => 10, 'achievement_points' => 10,
        ]);
    }

    private function financeFixture(int $studentCount = 1): array
    {
        $year = AcademicYear::query()->create(['name' => '2026/2027', 'is_active' => true]);
        $teacher = User::query()->create(['name' => 'Wali Kelas', 'email' => 'teacher@test.test', 'role' => 'teacher', 'password' => 'password', 'is_active' => true]);
        $tu = User::query()->create(['name' => 'Petugas TU', 'email' => 'tu@test.test', 'role' => 'tu', 'password' => 'password', 'is_active' => true]);
        $class = SchoolClass::query()->create([
            'name' => 'X A', 'grade_level' => 10, 'academic_year_id' => $year->id, 'homeroom_teacher_id' => $teacher->id,
        ]);
        $students = collect();

        foreach (range(1, $studentCount) as $number) {
            $student = User::query()->create([
                'name' => "Siswa {$number}", 'email' => "student{$number}@test.test", 'role' => 'student', 'password' => 'password', 'is_active' => true,
            ]);
            StudentProfile::query()->create(['user_id' => $student->id, 'school_class_id' => $class->id, 'nis' => "NIS{$number}"]);
            $students->push($student);
        }

        return [$teacher, $tu, $class, $students, $year];
    }
}
