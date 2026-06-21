<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $year = AcademicYear::query()->firstOrCreate([
            'name' => '2026/2027',
        ], [
            'starts_on' => '2026-07-01',
            'ends_on' => '2027-06-30',
            'is_active' => true,
        ]);

        SchoolClass::query()->firstOrCreate([
            'name' => 'X IPA 1',
        ], [
            'academic_year_id' => $year->id,
            'grade_level' => 10,
        ]);

        SchoolSetting::query()->firstOrCreate([], [
            'school_name' => 'Sekolah Ibrahim',
            'attendance_radius_meters' => 100,
            'start_time' => '07:00',
            'late_after' => '07:00',
        ]);

        User::query()->firstOrCreate([
            'email' => 'admin@sekolah.test',
        ], [
            'name' => 'Admin Sekolah',
            'role' => 'admin',
            'password' => Hash::make('password123'),
            'must_change_password' => false,
        ]);

        Achievement::query()->firstOrCreate(['code' => 'ONTIME_7_DAYS'], [
            'name' => 'Tepat Waktu 7 Hari',
            'description' => 'Hadir tepat waktu selama tujuh hari aktif.',
        ]);
    }
}
