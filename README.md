# Absensi dan Penilaian Sikap Sekolah

Aplikasi Laravel untuk absensi siswa, penilaian sikap, poin prestasi/pelanggaran, monitoring orang tua, dan persiapan integrasi absensi fingerprint IoT.

## Fitur MVP

- Login role admin, guru, siswa, dan orang tua.
- Admin membuat akun pengguna.
- Admin mengatur kelas.
- Admin menghubungkan orang tua dengan siswa.
- Admin mengatur lokasi sekolah, radius absensi, dan jam masuk.
- Admin mendaftarkan perangkat fingerprint IoT.
- Siswa absen dari dashboard dengan validasi lokasi browser.
- Guru memberi penilaian sikap.
- Guru memberi poin prestasi, pencapaian, atau pelanggaran.
- Sistem menghitung point general, point sikap, point absen, dan point prestasi.
- Sistem memberi label siswa otomatis.
- Orang tua melihat ringkasan poin dan label anak.
- Export laporan absensi dan raport sikap dalam format CSV.
- API fingerprint IoT di `POST /api/iot/attendance`.

## Dokumentasi

Dokumentasi project ada di folder `docs`:

- `docs/project-overview.md`
- `docs/task-breakdown.md`
- `docs/iot-fingerprint-integration.md`

## Setup Development

Install dependency PHP:

```bash
composer install
```

Siapkan environment:

```bash
copy .env.example .env
php artisan key:generate
```

Siapkan database SQLite:

```bash
php artisan migrate:fresh --seed
```

Jalankan server:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Buka:

```text
http://127.0.0.1:8000/login
```

Dashboard demo untuk presentasi client:

```text
http://127.0.0.1:8000/demo-dashboard
```

## Akun Admin Default

Seeder membuat akun admin:

```text
Email: admin@sekolah.test
Password: password123
```

## Testing

Jalankan test:

```bash
php artisan test
```

Format kode:

```bash
vendor/bin/pint --dirty
```

## Integrasi Fingerprint IoT

Endpoint:

```text
POST /api/iot/attendance
```

Contoh payload:

```json
{
  "device_identifier": "FP-GATE-01",
  "api_token": "token-perangkat",
  "fingerprint_user_id": "100124",
  "scanned_at": "2026-06-22 06:45:00"
}
```

Untuk MVP, `fingerprint_user_id` dicocokkan ke `nis` siswa.

Detail teknis ada di `docs/iot-fingerprint-integration.md`.

# absen-ibrahim
