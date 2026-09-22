<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\LandingItem;
use App\Models\LandingPage;
use App\Models\LandingTuitionPackage;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\SchoolFeeType;
use App\Models\SchoolSetting;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\TeacherProfile;
use App\Models\StudentProfile;
use App\Models\StudentClassHistory;
use App\Models\StudentPointSummary;
use App\Models\ParentProfile;
use App\Models\Attendance;
use App\Models\StudentBill;
use App\Models\StudentPayment;
use App\Models\PointTransaction;
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

        Semester::query()->firstOrCreate([
            'academic_year_id' => $year->id,
            'name' => 'Ganjil',
        ], [
            'starts_on' => '2026-07-01',
            'ends_on' => '2026-12-31',
            'is_active' => true,
        ]);

        Semester::query()->firstOrCreate([
            'academic_year_id' => $year->id,
            'name' => 'Genap',
        ], [
            'starts_on' => '2027-01-01',
            'ends_on' => '2027-06-30',
            'is_active' => false,
        ]);

        SchoolSetting::query()->firstOrCreate([], [
            'school_name' => 'Sekolah Ibrahim',
            'attendance_radius_meters' => 100,
            'max_location_accuracy_meters' => 100,
            'attendance_open_time' => '05:00',
            'start_time' => '07:00',
            'late_after' => '07:00',
            'attendance_close_time' => '10:00',
        ]);

        LandingPage::query()->firstOrCreate([], [
            ...LandingPage::defaults(),
            'hero_image_url' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1400&q=85',
            'whatsapp' => '6281234567890',
            'email' => 'info@sekolahibrahim.sch.id',
            'address' => 'Jl. Pendidikan No. 1, Indonesia',
        ]);

        $landingItems = [
            ['kind' => 'statistic', 'title' => '420+', 'kicker' => 'Peserta didik', 'sort_order' => 1],
            ['kind' => 'statistic', 'title' => '32', 'kicker' => 'Tenaga pendidik', 'sort_order' => 2],
            ['kind' => 'statistic', 'title' => '18', 'kicker' => 'Program pilihan', 'sort_order' => 3],
            ['kind' => 'statistic', 'title' => '12 th', 'kicker' => 'Membersamai keluarga', 'sort_order' => 4],
            ['kind' => 'program', 'title' => 'Tahfiz & pembiasaan adab', 'kicker' => 'Iman', 'body' => 'Hafalan bertahap, murajaah, salat berjamaah, dan adab harian yang dibangun dengan teladan.', 'sort_order' => 1],
            ['kind' => 'program', 'title' => 'Sains berbasis eksplorasi', 'kicker' => 'Ilmu', 'body' => 'Anak belajar bertanya, mengamati, mencoba, lalu mengomunikasikan temuannya dengan percaya diri.', 'sort_order' => 2],
            ['kind' => 'program', 'title' => 'Kepemimpinan & kemandirian', 'kicker' => 'Amal', 'body' => 'Proyek sosial, kegiatan alam, dan tanggung jawab kelas menjadi ruang latihan yang nyata.', 'sort_order' => 3],
            ['kind' => 'activity', 'title' => 'Belajar sains dari kebun sekolah', 'kicker' => 'Pembelajaran kontekstual', 'body' => 'Siswa mengamati ekosistem kecil dan mencatat perubahan yang mereka temukan.', 'image_url' => 'https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=1200&q=80', 'published_at' => now()->subDays(4)->toDateString(), 'sort_order' => 1],
            ['kind' => 'activity', 'title' => 'Tasmi’ dan apresiasi capaian hafalan', 'kicker' => 'Kabar sekolah', 'body' => 'Ruang apresiasi untuk proses, kedisiplinan, dan keberanian setiap peserta didik.', 'image_url' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1200&q=80', 'published_at' => now()->subDays(12)->toDateString(), 'sort_order' => 2],
            ['kind' => 'activity', 'title' => 'Hari karya: dari gagasan menjadi manfaat', 'kicker' => 'Karya siswa', 'body' => 'Presentasi lintas kelas yang mengajak anak menjelaskan proses, bukan hanya hasil akhir.', 'image_url' => 'https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?auto=format&fit=crop&w=1200&q=80', 'published_at' => now()->subDays(20)->toDateString(), 'sort_order' => 3],
            ['kind' => 'testimonial', 'title' => 'Ummi Rahma', 'kicker' => 'Orang tua siswa kelas 6', 'body' => 'Yang paling terasa bukan hanya perkembangan akademiknya, tetapi cara anak kami mulai bertanggung jawab pada pilihan dan kebiasaan hariannya.', 'sort_order' => 1],
        ];

        foreach ($landingItems as $item) {
            LandingItem::query()->firstOrCreate(
                ['kind' => $item['kind'], 'title' => $item['title']],
                [...$item, 'is_active' => true],
            );
        }

        $tuitionPackages = [
            [
                'name' => 'Uang Pangkal',
                'price' => 7500000,
                'billing_period' => 'sekali saat masuk',
                'description' => 'Persiapan awal pendidikan dan kebutuhan peserta didik baru.',
                'features' => ['Pengembangan sarana pendidikan', 'Seragam sekolah', 'Buku dan modul awal', 'Kegiatan orientasi siswa'],
                'sort_order' => 1,
            ],
            [
                'name' => 'Paket Pendidikan',
                'price' => 1250000,
                'billing_period' => 'per bulan',
                'description' => 'Pembelajaran terpadu untuk kegiatan akademik dan pembinaan harian.',
                'features' => ['Pembelajaran akademik', 'Tahfiz dan pembinaan adab', 'Ekstrakurikuler reguler', 'Laporan perkembangan siswa'],
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Kegiatan Tahunan',
                'price' => 2500000,
                'billing_period' => 'per tahun',
                'description' => 'Mendukung pengalaman belajar dan kegiatan pengembangan karakter.',
                'features' => ['Kemah dan kegiatan kepemimpinan', 'Proyek sosial', 'Kunjungan pembelajaran', 'Pentas karya siswa'],
                'sort_order' => 3,
            ],
        ];

        foreach ($tuitionPackages as $package) {
            LandingTuitionPackage::query()->firstOrCreate(
                ['name' => $package['name']],
                [
                    ...$package,
                    'cta_label' => 'Tanya paket ini',
                    'cta_url' => '#pendaftaran',
                    'is_active' => true,
                    'is_featured' => $package['is_featured'] ?? false,
                ],
            );
        }

        $admin = User::query()->firstOrCreate([
            'email' => 'admin@sekolah.test',
        ], [
            'name' => 'Admin Sekolah',
            'username' => 'admin',
            'role' => 'superadmin',
            'password' => Hash::make('password123'),
            'must_change_password' => false,
        ]);

        $tu = User::query()->firstOrCreate([
            'email' => 'tu@sekolah.test',
        ], [
            'name' => 'Tata Usaha Sekolah',
            'username' => 'tu',
            'role' => 'tu',
            'password' => Hash::make('password123'),
            'must_change_password' => false,
        ]);

        $admin->roles()->sync([Role::query()->where('name', 'superadmin')->value('id')]);
        $tu->roles()->sync([Role::query()->where('name', 'tu')->value('id')]);

        $demoClasses = collect([
            ['name' => '10A', 'grade_level' => 10],
            ['name' => '11B', 'grade_level' => 11],
            ['name' => '12A', 'grade_level' => 12],
        ])->mapWithKeys(function (array $data) use ($year) {
            $class = SchoolClass::query()->firstOrCreate(['name' => $data['name']], [
                'academic_year_id' => $year->id,
                'grade_level' => $data['grade_level'],
            ]);

            return [$data['name'] => $class];
        });

        $english = Subject::query()->firstOrCreate(['code' => 'BIG'], ['name' => 'Bahasa Inggris']);
        $mathematics = Subject::query()->firstOrCreate(['code' => 'MTK'], ['name' => 'Matematika']);
        $science = Subject::query()->firstOrCreate(['code' => 'IPA'], ['name' => 'Ilmu Pengetahuan Alam']);

        $englishTeacher = User::query()->firstOrCreate(['username' => 'budi.santoso'], [
            'name' => 'Budi Santoso', 'email' => 'budi@sekolah.test', 'role' => 'teacher',
            'password' => Hash::make('password123'), 'must_change_password' => false,
        ]);
        $mathTeacher = User::query()->firstOrCreate(['username' => 'siti.rahma'], [
            'name' => 'Siti Rahma', 'email' => 'siti.rahma@sekolah.test', 'role' => 'teacher',
            'password' => Hash::make('password123'), 'must_change_password' => false,
        ]);
        foreach ([[$englishTeacher, 'G-001'], [$mathTeacher, 'G-002']] as [$teacher, $nip]) {
            $teacher->roles()->syncWithoutDetaching([Role::query()->where('name', 'teacher')->value('id')]);
            TeacherProfile::query()->firstOrCreate(['user_id' => $teacher->id], ['employee_number' => $nip]);
        }

        $demoClasses['10A']->update(['homeroom_teacher_id' => $englishTeacher->id]);
        $demoClasses['11B']->update(['homeroom_teacher_id' => $mathTeacher->id]);
        TeachingAssignment::query()->firstOrCreate(['teacher_user_id' => $englishTeacher->id, 'school_class_id' => $demoClasses['11B']->id, 'subject_id' => $english->id]);
        TeachingAssignment::query()->firstOrCreate(['teacher_user_id' => $englishTeacher->id, 'school_class_id' => $demoClasses['12A']->id, 'subject_id' => $english->id]);
        TeachingAssignment::query()->firstOrCreate(['teacher_user_id' => $mathTeacher->id, 'school_class_id' => $demoClasses['10A']->id, 'subject_id' => $mathematics->id]);
        TeachingAssignment::query()->firstOrCreate(['teacher_user_id' => $mathTeacher->id, 'school_class_id' => $demoClasses['11B']->id, 'subject_id' => $science->id]);

        $studentNames = [
            '10A' => ['Ahmad Fauzan', 'Nabila Putri', 'Rizky Ramadhan', 'Aisyah Zahra'],
            '11B' => ['Fajar Hidayat', 'Salma Aulia', 'Dimas Pratama', 'Hana Safitri'],
            '12A' => ['Rafi Akbar', 'Nadia Larasati', 'Yusuf Maulana', 'Kayla Anindya'],
        ];
        $demoStudents = collect();
        $studentNumber = 1;
        foreach ($studentNames as $className => $names) {
            foreach ($names as $name) {
                $nis = '2026'.str_pad((string) $studentNumber, 4, '0', STR_PAD_LEFT);
                $student = User::query()->firstOrCreate(['username' => 'siswa'.$studentNumber], [
                    'name' => $name, 'role' => 'student', 'password' => Hash::make('password123'), 'must_change_password' => false,
                ]);
                $student->roles()->syncWithoutDetaching([Role::query()->where('name', 'student')->value('id')]);
                StudentProfile::query()->updateOrCreate(['user_id' => $student->id], ['school_class_id' => $demoClasses[$className]->id, 'nis' => $nis]);
                StudentClassHistory::query()->firstOrCreate([
                    'student_user_id' => $student->id, 'school_class_id' => $demoClasses[$className]->id, 'ended_on' => null,
                ], ['academic_year_id' => $year->id, 'started_on' => $year->starts_on ?? today()]);
                $attitudePoints = 10 + $studentNumber;
                $attendancePoints = 5 + $studentNumber;
                $achievementPoints = $studentNumber * 2;
                StudentPointSummary::query()->updateOrCreate(['student_user_id' => $student->id], [
                    'general_points' => $attitudePoints + $attendancePoints + $achievementPoints, 'attitude_points' => $attitudePoints,
                    'attendance_points' => $attendancePoints, 'achievement_points' => $achievementPoints,
                    'label' => $studentNumber >= 9 ? 'Berkembang Baik' : 'Perlu Dipantau',
                ]);
                foreach ([
                    ['type' => 'attitude', 'points' => $attitudePoints, 'description' => 'Konsisten menunjukkan sikap tanggung jawab'],
                    ['type' => 'attendance', 'points' => $attendancePoints, 'description' => 'Kehadiran dan ketepatan waktu bulan ini'],
                    ['type' => 'achievement', 'points' => $achievementPoints, 'description' => 'Aktif berkontribusi dalam kegiatan kelas'],
                ] as $transaction) {
                    PointTransaction::query()->updateOrCreate([
                        'student_user_id' => $student->id, 'type' => $transaction['type'], 'description' => $transaction['description'],
                    ], ['actor_user_id' => $englishTeacher->id, 'points' => $transaction['points']]);
                }
                Attendance::query()->updateOrCreate(['student_user_id' => $student->id, 'attendance_date' => today()], [
                    'school_class_id' => $demoClasses[$className]->id, 'academic_year_id' => $year->id,
                    'checked_in_at' => $studentNumber % 4 === 0 ? '07:08:00' : '06:45:00', 'status' => 'present',
                    'source' => $studentNumber % 2 === 0 ? 'iot' : 'web', 'is_ontime' => $studentNumber % 4 !== 0,
                ]);
                $demoStudents->push($student);
                $studentNumber++;
            }
        }

        $parent = User::query()->firstOrCreate(['username' => 'orangtua.demo'], [
            'name' => 'Orang Tua Demo', 'email' => 'orangtua@sekolah.test', 'role' => 'parent',
            'password' => Hash::make('password123'), 'must_change_password' => false,
        ]);
        $parent->roles()->syncWithoutDetaching([Role::query()->where('name', 'parent')->value('id')]);
        ParentProfile::query()->firstOrCreate(['user_id' => $parent->id], ['phone' => '628123456789']);
        $parent->children()->syncWithoutDetaching([$demoStudents->first()->id => ['relationship' => 'parent']]);

        SchoolFeeType::query()->firstOrCreate(['code' => 'SPP'], [
            'name' => 'SPP',
            'billing_cycle' => 'monthly',
            'default_amount' => 250000,
        ]);

        SchoolFeeType::query()->firstOrCreate(['code' => 'BANGUNAN'], [
            'name' => 'Uang Bangunan',
            'billing_cycle' => 'annual',
            'default_amount' => 1000000,
        ]);

        $spp = SchoolFeeType::query()->where('code', 'SPP')->first();
        $demoBill = StudentBill::query()->firstOrCreate([
            'student_user_id' => $demoStudents->first()->id,
            'school_fee_type_id' => $spp->id,
            'billing_period' => '2026-09',
        ], [
            'school_class_id' => $demoClasses['10A']->id, 'academic_year_id' => $year->id,
            'created_by_user_id' => $tu->id, 'title' => 'SPP - 2026-09', 'amount' => 250000,
            'due_date' => '2026-09-10', 'paid_amount' => 100000, 'status' => 'partial',
        ]);
        StudentPayment::query()->firstOrCreate([
            'student_bill_id' => $demoBill->id, 'reference' => 'DEMO-PAY-001',
        ], [
            'submitted_by_user_id' => $parent->id, 'received_by_user_id' => $tu->id,
            'reviewed_by_user_id' => $tu->id, 'amount' => 100000, 'status' => 'verified',
            'paid_on' => '2026-09-05', 'payment_method' => 'transfer', 'reviewed_at' => now(),
            'notes' => 'Contoh pembayaran sebagian oleh orang tua.',
        ]);

        Achievement::query()->firstOrCreate(['code' => 'ONTIME_7_DAYS'], [
            'name' => 'Tepat Waktu 7 Hari',
            'description' => 'Hadir tepat waktu selama tujuh hari aktif.',
        ]);
    }
}
