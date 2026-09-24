# Absensi web dengan lokasi

Siswa masuk melalui akun sendiri, membuka dashboard, lalu menekan **Periksa GPS & absen**. Lokasi dibaca hanya setelah tindakan siswa. Pemeriksaan berhenti setelah berhasil, gagal, dibatalkan, halaman ditinggalkan, atau mencapai 30 detik; tidak ada pelacakan lokasi di latar belakang.

## Persiapan sekolah

1. Di **Sekolah & IoT → Profil & absensi**, isi koordinat sekolah yang sebenarnya, radius, galat maksimum GPS, jam buka, batas tepat waktu, dan jam tutup. Koordinat seeder adalah contoh, bukan bukti lokasi sekolah.
2. Gunakan HTTPS dengan sertifikat valid ketika mengakses lewat HP. HTTP localhost dapat digunakan untuk pengembangan di perangkat yang sama; HTTP alamat IP LAN tidak cukup untuk geolokasi HP.
3. Set `APP_ENV=production`, `APP_DEBUG=false`, URL HTTPS, cookie sesi aman, dan zona waktu sekolah (default `Asia/Jakarta`) ketika deploy. Jika TLS berhenti di reverse proxy, konfigurasi hanya IP proxy tepercaya di Laravel agar `Request::isSecure()` mengenali HTTPS. Jangan mempercayai semua sumber header proxy.
4. Pastikan waktu server tersinkronisasi dan tanggal/jam HP otomatis. Server menentukan tanggal, jam masuk, tepat waktu, dan terlambat.
5. Lakukan uji lapangan di HP Android/iPhone yang dipakai siswa sebelum peluncuran, terutama akurasi GPS di dalam gedung dan batas radius.

## Validasi yang diterapkan

- Login, akun aktif, izin `attendance.checkin`, dan role siswa wajib; POST menggunakan CSRF. Identitas siswa berasal dari sesi, bukan input formulir.
- Browser meminta lokasi presisi (`enableHighAccuracy`, `maximumAge: 0`). Pembacaan yang kurang akurat ditunggu sampai membaik. Lokasi tidak tersedia/izin ditolak tidak dapat dikirim melalui antarmuka normal.
- Server menolak koordinat di luar rentang, galat nol/negatif atau melebihi batas, lokasi di luar radius, dan lokasi yang jarak ditambah galatnya melewati radius. Contoh: radius 100 m, jarak 80 m, galat ±30 m ditolak.
- Timestamp pembacaan wajib; umur lebih dari 60 detik atau lebih dari 10 detik di masa depan ditolak server. Browser hanya mengirim fix berumur maksimal 15 detik. Timestamp klien ini mendeteksi data usang, bukan membuktikan keaslian lokasi.
- Batas jam dibandingkan sebagai `HH:mm:ss`, termasuk jam buka, batas tepat waktu, dan jam tutup. Konfigurasi `07:00` berarti `07:00:00`; setelah detik itu dianggap terlambat. Check-in baru di luar jam buka–tutup ditolak.
- Absensi pertama per siswa per tanggal dan poinnya tidak ditimpa/ditambah oleh kiriman ulang. Database memiliki batas unik siswa/tanggal; pembuatan absensi dan poin memakai transaksi.
- Maksimal 10 permintaan check-in per menit per akun melalui limiter Laravel. Produksi menolak pengiriman lewat HTTP.
- Rekap dan ekspor menggunakan kelas yang tersimpan saat absen, bukan kelas siswa saat laporan dibuka; jarak dan galat GPS tersimpan untuk pemeriksaan.

## Batas anti-kecurangan

Geolocation browser bergantung pada izin dan penyedia lokasi perangkat (GPS, Wi-Fi, atau layanan lokasi lain). Web tidak dapat membuktikan sensor GPS fisik aktif, mendeteksi semua Fake GPS/DevTools, atau menjamin akun tidak dipinjamkan. Pengguna yang sengaja memodifikasi kiriman masih dapat mengaku berada di sekolah. HTTPS, pemeriksaan umur fix, dan radius bukan bukti anti-spoofing atau verifikasi identitas fisik.

Untuk kebutuhan pembuktian kehadiran yang lebih kuat, diperlukan faktor independen yang dikendalikan sekolah, misalnya pemeriksaan guru atau integrasi perangkat sekolah. Jangan menyatakan mekanisme GPS web ini 100% anti-kecurangan.

Referensi browser: [watchPosition, HTTPS dan izin lokasi](https://developer.mozilla.org/en-US/docs/Web/API/Geolocation/watchPosition), [akurasi lokasi merupakan estimasi dengan tingkat keyakinan 95%](https://developer.mozilla.org/en-US/docs/Web/API/GeolocationCoordinates/accuracy).

## Verifikasi

Pengujian server menggunakan database SQLite dalam memori:

```sh
php artisan test --compact --filter='AttendanceGpsSecurityTest|AttendanceWorkflowTest|IotAttendanceTest|AccessControlTest|StudentProgressDashboardTest'
node --test tests/js/attendance-check-in.test.mjs
```

Tes JavaScript mensimulasikan API lokasi; bukan pengukuran GPS fisik. Uji lapangan tetap diperlukan: lokasi presisi aktif di dalam sekolah; izin ditolak/GPS tidak tersedia; di luar sekolah; dekat batas radius; jaringan putus; jam buka/tutup; kiriman ulang; serta hasil rekap dan poin.
