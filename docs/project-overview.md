# Project Overview - Absensi dan Penilaian Sikap Sekolah

## Ringkasan

Project ini adalah aplikasi Laravel untuk kebutuhan sekolah dalam mengelola:

- Absensi siswa berbasis lokasi.
- Absensi siswa dari perangkat IoT fingerprint.
- Penilaian sikap atau soft skill.
- Poin prestasi, pencapaian, dan pelanggaran.
- Monitoring siswa oleh guru, siswa, dan orang tua.
- Label otomatis agar guru lebih aware terhadap siswa yang butuh perhatian.
- Export laporan absensi dan raport sikap.

Sistem memakai pendekatan role-based access dengan lima jenis pengguna:

- Admin
- Guru
- Siswa
- Orang tua
- Tata Usaha (TU)

Untuk versi awal, akun guru, siswa, dan orang tua dibuat oleh admin. Pengguna tidak melakukan registrasi mandiri.

## Tujuan Sistem

Tujuan utama sistem adalah membuat sekolah punya satu aplikasi terpusat untuk memantau kedisiplinan, sikap, dan pencapaian siswa secara berkala.

Sistem tidak ditujukan sebagai pengganti nilai raport akademik. Penilaian yang dikelola adalah penilaian sikap, soft skill, pencapaian kelas, prestasi, absensi, dan indikator perilaku lain yang berguna untuk pembinaan siswa.

## Stack Teknis

- Backend: Laravel 13
- Bahasa: PHP 8.3+
- Database default: SQLite untuk development
- UI: Blade template dengan CSS internal sederhana
- Testing: PHPUnit bawaan Laravel
- Export awal: CSV yang bisa dibuka di Excel

Package tambahan seperti Laravel Breeze, Spatie Permission, atau Maatwebsite Excel belum dipasang agar MVP tetap ringan. Package tersebut bisa ditambahkan pada fase berikutnya jika dibutuhkan.

## Role dan Hak Akses

### Admin

Admin bertanggung jawab mengelola data utama sistem.

Fitur admin:

- Login ke dashboard admin.
- Membuat akun superadmin, TU, guru, siswa, orang tua, dan role kustom.
- Mengatur kelas.
- Menghubungkan akun orang tua dengan akun siswa.
- Mengatur lokasi sekolah untuk validasi absensi GPS.
- Mengatur radius absensi.
- Mengatur jam masuk dan batas tepat waktu.
- Mendaftarkan perangkat fingerprint IoT.
- Melihat dashboard umum.

### Guru

Guru bertanggung jawab melakukan penilaian dan monitoring siswa.

Fitur guru:

- Melihat dashboard siswa dan label perhatian.
- Melihat rekap absensi.
- Memberikan penilaian sikap.
- Memberikan poin prestasi, pencapaian, atau pelanggaran.
- Mengunduh laporan absensi.
- Mengunduh raport sikap dalam bentuk CSV.

### Siswa

Siswa memakai sistem untuk absensi dan memantau perkembangan dirinya.

Fitur siswa:

- Login ke dashboard siswa.
- Melakukan absensi berbasis lokasi browser.
- Melihat poin general.
- Melihat poin sikap.
- Melihat poin absensi.
- Melihat poin prestasi.
- Melihat label perkembangan diri.
- Melihat kelas, ringkasan kehadiran, progres level, dan timeline aktivitas per semester/kelas.
- Tidak memiliki akses ke data nominal keuangan.

### Orang Tua

Orang tua memakai sistem untuk memantau perkembangan anak.

Fitur orang tua:

- Login ke dashboard orang tua.
- Melihat anak yang sudah dihubungkan oleh admin.
- Melihat poin anak.
- Melihat label perkembangan anak.
- Melihat profil perkembangan, kehadiran, histori kelas, dan timeline anak.
- Tidak memiliki akses ke data nominal keuangan anak.

### Tata Usaha (TU)

- Membuat master dan menerbitkan tagihan sekolah.
- Mencatat pembayaran atau angsuran per siswa.
- Memverifikasi usulan biaya dinamis dari wali kelas.
- Memantau tunggakan yang tetap terbawa saat siswa berpindah kelas.

## Modul Utama

### 1. Authentication dan Role

Sistem memakai login username atau email dan password. Akun hasil import wajib mengganti password awal saat login pertama.

Role utama tersimpan di akun dan terhubung ke tabel RBAC role/permission. Role sistem awal:

- `superadmin`
- `tu`
- `teacher`
- `student`
- `parent`

Superadmin dapat membuat role kustom dan memilih permission. Middleware permission membatasi route sesuai kewenangan akun.

### 2. Data Master

Data master awal:

- Tahun ajaran
- Kelas
- Mata pelajaran
- Profil siswa
- Profil guru
- Profil orang tua
- Relasi orang tua dan siswa
- Pengaturan sekolah
- Perangkat IoT

### 3. Absensi GPS

Siswa dapat melakukan absensi dari dashboard.

Alur:

1. Siswa klik tombol absensi.
2. Browser membaca latitude dan longitude siswa.
3. Sistem membandingkan lokasi siswa dengan lokasi sekolah.
4. Sistem menolak hasil GPS yang tingkat galatnya melebihi batas admin.
5. Jika siswa berada dalam radius maksimum sekolah, absensi diterima.
6. Jika di luar radius, absensi ditolak.
7. Sistem menentukan status tepat waktu atau terlambat.
8. Sistem membuat transaksi poin absensi.

Parameter yang digunakan:

- Latitude sekolah
- Longitude sekolah
- Radius absensi dalam meter
- Maksimum galat/akurasi GPS dalam meter
- Jam masuk
- Batas tepat waktu

### 4. Absensi IoT Fingerprint

Sistem sudah disiapkan agar perangkat fingerprint dapat mengirim data absensi ke API.

Endpoint:

```text
POST /api/iot/attendance
```

Payload JSON:

```json
{
  "device_identifier": "FP-GATE-01",
  "api_token": "token-dari-admin",
  "fingerprint_user_id": "NIS-atau-ID-fingerprint",
  "scanned_at": "2026-06-22 06:45:00"
}
```

Catatan penting:

- `device_identifier` adalah ID perangkat yang didaftarkan admin.
- `api_token` dibuat saat admin mendaftarkan perangkat.
- `fingerprint_user_id` saat ini dipetakan ke `nis` siswa.
- `scanned_at` opsional. Jika tidak dikirim, sistem memakai waktu server.

Alur IoT:

1. Perangkat fingerprint membaca sidik jari siswa.
2. Perangkat mendapatkan ID fingerprint siswa.
3. Perangkat mengirim request ke API Laravel.
4. Laravel memvalidasi device identifier dan token.
5. Laravel mencari siswa berdasarkan NIS/fingerprint ID.
6. Jika cocok, sistem membuat absensi dengan source `fingerprint`.
7. Sistem membuat poin absensi otomatis.
8. Log request IoT tetap disimpan untuk audit.

### 5. Penilaian Sikap

Guru dapat memberi penilaian sikap siswa.

Aspek awal:

- Disiplin
- Tanggung Jawab
- Kerja Sama
- Kejujuran
- Sopan Santun
- Kepemimpinan

Skor:

- 5 = Sangat Baik
- 4 = Baik
- 3 = Cukup
- 2 = Kurang
- 1 = Buruk

Konversi poin:

| Skor | Poin |
|---|---:|
| 5 | +5 |
| 4 | +3 |
| 3 | +1 |
| 2 | -3 |
| 1 | -5 |

### 6. Prestasi, Pencapaian, dan Pelanggaran

Guru bisa memberikan poin manual untuk:

- Prestasi
- Pencapaian kelas
- Keaktifan
- Perkembangan positif
- Pelanggaran

Poin bisa positif atau negatif.

Contoh:

- Aktif bertanya: +5
- Membantu teman: +10
- Prestasi lomba: +20
- Pelanggaran disiplin: -10
- Pelanggaran berat: -50

### 7. Sistem Poin dan Label

Jenis poin:

- Point General
- Point Sikap
- Point Absen
- Point Prestasi

Formula awal:

```text
Point General = Point Sikap + Point Absen + Point Prestasi
```

Label otomatis:

| Point General | Label |
|---:|---|
| >= 100 | Teladan |
| 50 sampai 99 | Berkembang Baik |
| 0 sampai 49 | Perlu Dipantau |
| -1 sampai -49 | Perlu Pembinaan |
| <= -50 | Prioritas Perhatian Guru |

Jika siswa mencapai `<= -50`, siswa diberi label `Prioritas Perhatian Guru`.

### 8. Report dan Export

Export awal menggunakan CSV agar bisa dibuka di Excel.

Report yang tersedia:

- Laporan absensi
- Raport sikap / rekap poin siswa

Route:

```text
GET /reports/attendance.csv
GET /reports/attitude.csv
```

Pada fase berikutnya, export bisa ditingkatkan menggunakan package `maatwebsite/excel` agar mendukung formatting Excel yang lebih rapi.

## Database Utama

Tabel yang sudah disiapkan:

- `users`
- `academic_years`
- `school_classes`
- `subjects`
- `student_profiles`
- `teacher_profiles`
- `parent_profiles`
- `parent_student`
- `teaching_assignments`
- `school_settings`
- `attendances`
- `attitude_assessments`
- `achievement_assessments`
- `point_transactions`
- `student_point_summaries`
- `achievements`
- `student_achievements`
- `iot_devices`
- `iot_attendance_logs`

## Akun Default Development

Seeder membuat akun admin awal:

```text
Email: admin@sekolah.test
Password: password123
Role: admin
```

## Status Implementasi Saat Ini

Sudah dibuat:

- Scaffold Laravel.
- Migrasi database utama.
- Model Eloquent utama.
- Middleware role.
- Login dan logout.
- Dashboard role-aware.
- Halaman admin untuk akun.
- Halaman admin untuk pengaturan sekolah.
- Halaman admin untuk kelas.
- Halaman admin untuk perangkat fingerprint IoT.
- Relasi orang tua dan siswa.
- Absensi siswa berbasis GPS.
- Endpoint absensi IoT fingerprint.
- Penilaian sikap oleh guru.
- Penilaian prestasi/pelanggaran oleh guru.
- Kalkulasi poin otomatis.
- Label otomatis siswa.
- Export CSV absensi.
- Export CSV raport sikap.
- Test untuk route dasar, kalkulasi label, dan absensi IoT.

## Verifikasi

Command yang sudah dijalankan:

```bash
php artisan migrate:fresh --seed
php artisan test
php artisan route:list --except-vendor
vendor/bin/pint --dirty
```

Hasil test terakhir:

```text
4 tests passed
12 assertions passed
```
