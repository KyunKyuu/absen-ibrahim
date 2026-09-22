# Task Breakdown - Absensi dan Penilaian Sikap Sekolah

## Phase 1 - Fondasi Project

Status: Selesai

- Scaffold Laravel project.
- Setup database development.
- Buat migrasi utama.
- Buat model utama.
- Buat seeder admin awal.
- Buat struktur route web dan API.
- Buat middleware role.
- Buat layout Blade dasar.

## Phase 2 - Authentication dan User Management

Status: Selesai untuk MVP

- Login pengguna.
- Logout pengguna.
- RBAC superadmin, TU, guru, siswa, orang tua, dan role kustom.
- Admin membuat akun pengguna.
- Admin membuat akun siswa dengan NIS.
- Admin membuat akun guru.
- Admin membuat akun orang tua.
- Admin menghubungkan orang tua dengan siswa.
- Login menggunakan username atau email.
- Superadmin membuat role dan mengatur permission RBAC.
- Superadmin membuat akun dan mengganti role akun.
- Import maksimal 1.000 akun dari Google Sheet publik/shared-link.
- Password default import dan kewajiban mengganti password saat login pertama.

Task lanjutan:

- Reset password oleh admin.
- Status aktif/nonaktif akun di UI.
- Import akun siswa dari Excel.
- Bulk generate akun siswa dan orang tua.

## Phase 3 - Data Master Sekolah

Status: Sebagian selesai

Sudah selesai:

- Tahun ajaran awal lewat seeder.
- Kelas.
- Pengaturan sekolah.
- Lokasi sekolah.
- Radius absensi.
- Jam masuk.
- Batas tepat waktu.
- Penetapan wali kelas.

Task lanjutan:

- CRUD mata pelajaran.
- Mapping guru ke kelas dan mata pelajaran.
- Semester aktif.
- Tahun ajaran aktif dari UI.
- Import data kelas dan siswa dari Excel.

## Phase 4 - Absensi GPS

Status: Selesai untuk MVP

Sudah selesai:

- Siswa melakukan absensi dari dashboard.
- Browser mengambil latitude dan longitude.
- Sistem menghitung jarak ke sekolah.
- Sistem menolak absensi di luar radius.
- Admin mengatur radius maksimum dan batas galat GPS.
- Server memvalidasi akurasi GPS, role siswa, dan jam operasional.
- Dashboard menampilkan status absensi hari ini agar tidak submit berulang.
- Sistem menentukan ontime atau terlambat.
- Sistem membuat poin absensi otomatis.
- Data absensi tersimpan di tabel `attendances`.

Task lanjutan:

- Riwayat absensi di dashboard siswa.
- Rekap absensi per kelas.
- Filter absensi berdasarkan tanggal.
- Input izin, sakit, alfa oleh guru/admin.
- Bukti izin upload file.
- Jadwal masuk berbeda per kelas.
- Validasi tambahan untuk fake GPS.
- QR code harian sebagai lapisan validasi tambahan.

## Phase 5 - Absensi IoT Fingerprint

Status: Fondasi selesai

Sudah selesai:

- Tabel perangkat IoT.
- Tabel log absensi IoT.
- Admin mendaftarkan perangkat fingerprint.
- Sistem membuat token perangkat.
- Endpoint `POST /api/iot/attendance`.
- Validasi token perangkat.
- Mapping `fingerprint_user_id` ke NIS siswa.
- Absensi fingerprint masuk ke tabel absensi yang sama.
- Poin absensi fingerprint dihitung otomatis.
- Test feature absensi IoT.

Task lanjutan:

- UI daftar log request IoT.
- UI mapping fingerprint ID jika tidak ingin memakai NIS.
- Endpoint heartbeat perangkat.
- Endpoint sinkronisasi daftar siswa ke perangkat.
- Dokumentasi firmware contoh untuk ESP32/Raspberry Pi.
- Retry strategy jika internet perangkat putus.
- Signature HMAC agar payload lebih aman.
- IP allowlist untuk perangkat sekolah.

## Phase 6 - Penilaian Sikap

Status: Selesai untuk MVP

Sudah selesai:

- Guru memilih siswa.
- Guru memilih aspek sikap.
- Guru memilih skor 1 sampai 5.
- Sistem mengonversi skor menjadi poin.
- Sistem menyimpan catatan guru.
- Sistem membuat transaksi poin sikap.

Task lanjutan:

- Aspek sikap bisa dikustomisasi admin.
- Filter siswa berdasarkan kelas guru.
- Rubrik penilaian per aspek.
- Riwayat penilaian per siswa.
- Approval wali kelas untuk catatan sensitif.
- Grafik perkembangan sikap.

## Phase 7 - Prestasi, Pencapaian, dan Pelanggaran

Status: Selesai untuk MVP

Sudah selesai:

- Guru memberi poin positif atau negatif.
- Kategori awal: achievement dan violation.
- Sistem menyimpan catatan.
- Sistem membuat transaksi poin prestasi.

Task lanjutan:

- Master kategori prestasi.
- Master kategori pelanggaran.
- Batas maksimal poin berdasarkan kategori.
- Upload bukti prestasi.
- Approval admin untuk poin besar.
- Notifikasi ke orang tua untuk pelanggaran berat.

## Phase 8 - Poin, Label, dan Gamifikasi

Status: Fondasi selesai

Sudah selesai:

- Point General.
- Point Sikap.
- Point Absen.
- Point Prestasi.
- Kalkulasi summary otomatis.
- Label otomatis.
- Leaderboard dashboard untuk admin/guru.
- Achievement table disiapkan.

Task lanjutan:

- Award achievement otomatis.
- Achievement hadir 7 hari berturut-turut.
- Achievement tidak terlambat 1 bulan.
- Achievement sikap sangat baik beruntun.
- Leaderboard per kelas.
- Leaderboard per semester.
- Pengaturan apakah leaderboard terlihat ke siswa.
- Audit anti-stigma untuk siswa dengan poin negatif.

## Phase 9 - Dashboard

Status: Selesai untuk MVP

Sudah selesai:

- Dashboard admin.
- Dashboard guru.
- Dashboard siswa.
- Dashboard orang tua.
- Metrik total siswa, guru, orang tua.
- Metrik absensi hari ini.
- Metrik siswa prioritas.
- Ringkasan poin siswa.
- Profil perkembangan bergaya gamifikasi untuk siswa dan orang tua.
- Filter aktivitas berdasarkan semester dan riwayat kelas.
- Snapshot kelas/semester pada data absensi.

Task lanjutan:

- Grafik tren absensi.
- Grafik tren poin.
- Filter kelas.
- Detail profil siswa.
- Timeline aktivitas siswa.
- Dashboard wali kelas.

## Phase 10 - Report dan Export

Status: Selesai untuk MVP CSV

Sudah selesai:

- Export absensi CSV.
- Export raport sikap CSV.

Task lanjutan:

- Export Excel dengan `maatwebsite/excel`.
- Export PDF raport sikap.
- Template raport sikap sekolah.
- Filter laporan berdasarkan kelas, tanggal, semester.
- Laporan siswa prioritas perhatian.
- Laporan absensi bulanan.

## Phase 11 - Security dan Audit

Status: Sebagian selesai

Sudah selesai:

- Login dibatasi lima percobaan per menit.
- Sesi akun nonaktif diputus pada request berikutnya.
- Token API perangkat tidak disimpan dalam payload log.
- Export CSV dilindungi dari formula injection.
- Menu dan endpoint nominal keuangan tidak dapat diakses siswa/orang tua.
- Wali kelas hanya dapat melihat usulannya sendiri, bukan tagihan/pembayaran siswa.

Task:

- Audit log perubahan nilai dan poin.
- Audit log login.
- Pengaturan rate limit login yang dapat dikustomisasi.
- Ubah password pertama kali.
- Validasi permission lebih detail untuk guru.
- Guru hanya melihat kelas/mata pelajaran yang diajar.
- Hardening API IoT dengan HMAC signature.
- Rotasi token perangkat IoT.

## Phase 12 - Quality Assurance

Status: Berjalan

Sudah selesai:

- Test route redirect guest.
- Test kalkulasi label poin.
- Test endpoint IoT fingerprint.
- Test absensi GPS, radius, jam operasional, dan pencegahan absen ganda.
- Test validasi poin prestasi/pelanggaran.
- Test alur keuangan, otorisasi, angsuran, dan tunggakan.
- Laravel Pint formatter.

Task lanjutan:

- Test login role.
- Test admin membuat akun.
- Test guru input sikap.
- Test export CSV.
- Browser test untuk halaman utama.

## Phase 13 - Keuangan Siswa

Status: Selesai untuk MVP

Sudah selesai:

- Role Tata Usaha (TU).
- Master jenis tagihan seperti SPP dan uang bangunan.
- Penerbitan tagihan massal per kelas dan periode tanpa duplikasi.
- Pembayaran manual penuh atau sebagian per siswa oleh TU.
- Validasi agar pembayaran tidak melebihi sisa tagihan.
- Usulan biaya dinamis oleh wali kelas.
- Verifikasi atau penolakan usulan oleh TU sebelum terbit.
- Mode nominal per siswa dan target kolektif kelas.
- Histori kelas dan tunggakan yang terbawa saat naik/pindah kelas.
- Akses nominal/tagihan hanya untuk admin dan TU.

Task lanjutan:

- Nomor dan cetak kuitansi.
- Audit pembatalan/koreksi pembayaran.
- Diskon, beasiswa, denda, dan dispensasi.
- Rekonsiliasi bank atau payment gateway.
- Pengingat tagihan melalui WhatsApp/email.
- Promosi kelas secara massal.
- Laporan kas, piutang, dan tunggakan dalam PDF/Excel.

## Prioritas Berikutnya

Urutan task yang disarankan:

1. CRUD mata pelajaran dan mapping guru ke kelas.
2. Batasi akses guru hanya ke siswa yang diajar.
3. Tambah riwayat absensi dan riwayat poin di dashboard siswa/orang tua.
4. Tambah filter laporan.
5. Tambah import Excel untuk siswa dan akun.
6. Tambah export Excel native.
7. Tambah audit log.
8. Tambah dokumentasi dan contoh payload perangkat IoT.
