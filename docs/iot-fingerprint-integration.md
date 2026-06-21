# Integrasi IoT Fingerprint

## Tujuan

Dokumen ini menjelaskan rancangan integrasi perangkat fingerprint dengan aplikasi absensi sekolah.

Targetnya adalah agar perangkat fingerprint dapat mengirim data absensi ke Laravel dan data tersebut masuk ke sistem absensi yang sama dengan absensi GPS.

## Arsitektur Singkat

```text
Fingerprint Device
  -> membaca sidik jari
  -> mendapatkan fingerprint_user_id
  -> mengirim HTTP POST ke Laravel API
  -> Laravel validasi device dan token
  -> Laravel mapping fingerprint_user_id ke siswa
  -> Laravel membuat attendance
  -> Laravel membuat point transaction
```

## Endpoint

```text
POST /api/iot/attendance
```

Content-Type:

```text
application/json
```

## Payload

```json
{
  "device_identifier": "FP-GATE-01",
  "api_token": "token-perangkat",
  "fingerprint_user_id": "100124",
  "scanned_at": "2026-06-22 06:45:00"
}
```

## Field

| Field | Wajib | Keterangan |
|---|---|---|
| `device_identifier` | Ya | ID unik perangkat yang didaftarkan admin |
| `api_token` | Ya | Token rahasia perangkat |
| `fingerprint_user_id` | Ya | ID user fingerprint. Untuk MVP dipetakan ke NIS siswa |
| `scanned_at` | Tidak | Waktu scan dari perangkat. Jika kosong, server memakai waktu saat request diterima |

## Response Berhasil

```json
{
  "message": "Absensi fingerprint berhasil direkam.",
  "attendance_id": 1,
  "student": "Nama Siswa"
}
```

## Response Device Tidak Valid

```json
{
  "message": "Device tidak valid."
}
```

HTTP status:

```text
401 Unauthorized
```

## Response Fingerprint Tidak Cocok

```json
{
  "message": "Fingerprint ID belum terhubung ke NIS siswa.",
  "log_id": 1
}
```

HTTP status:

```text
422 Unprocessable Entity
```

## Cara Daftar Perangkat

Admin membuka:

```text
/admin/settings
```

Lalu mengisi:

- Nama perangkat
- Device identifier
- Kelas terkait jika perlu

Setelah disimpan, sistem menampilkan token perangkat satu kali. Token tersebut harus disimpan di konfigurasi perangkat atau firmware.

## Mapping Fingerprint ke Siswa

Untuk MVP, sistem memakai kolom `nis` pada `student_profiles` sebagai nilai yang dicocokkan dengan `fingerprint_user_id`.

Contoh:

```text
student_profiles.nis = 100124
payload fingerprint_user_id = 100124
```

Jika keduanya cocok, siswa dianggap valid.

Pada fase berikutnya, sebaiknya dibuat tabel khusus:

```text
student_fingerprint_mappings
```

Field yang disarankan:

- `id`
- `student_user_id`
- `device_id`
- `fingerprint_user_id`
- `fingerprint_template_hash`
- `is_active`
- `registered_at`

## Keamanan MVP

Keamanan yang sudah ada:

- Device identifier harus terdaftar.
- API token dicek dengan hash.
- Perangkat harus aktif.
- Endpoint memakai throttle.
- Semua request IoT disimpan di log.

## Keamanan Lanjutan

Untuk production, tambahkan:

- HMAC signature per request.
- Timestamp validation untuk mencegah replay attack.
- Token rotation.
- IP allowlist jika jaringan sekolah tetap.
- HTTPS wajib.
- Queue untuk proses absensi jika traffic besar.
- Monitoring perangkat offline.

## Contoh Request curl

```bash
curl -X POST http://127.0.0.1:8000/api/iot/attendance \
  -H "Content-Type: application/json" \
  -d '{
    "device_identifier": "FP-GATE-01",
    "api_token": "token-perangkat",
    "fingerprint_user_id": "100124",
    "scanned_at": "2026-06-22 06:45:00"
  }'
```

## Catatan Perangkat

Perangkat yang bisa dipakai:

- ESP32 dengan modul fingerprint dan WiFi.
- Raspberry Pi dengan scanner fingerprint USB.
- Mesin fingerprint komersial yang bisa webhook/API.

Syarat minimal perangkat:

- Bisa menyimpan device identifier.
- Bisa menyimpan API token.
- Bisa membaca fingerprint ID.
- Bisa mengirim HTTP POST JSON ke server Laravel.

## Alur Jika Internet Perangkat Terputus

Untuk fase lanjutan, perangkat sebaiknya punya offline queue.

Alur:

1. Fingerprint tetap direkam lokal.
2. Data disimpan di memori perangkat atau storage lokal.
3. Ketika internet kembali, perangkat mengirim backlog.
4. Payload tetap mengirim `scanned_at` asli.
5. Server membuat absensi berdasarkan waktu scan asli.

## Status Implementasi

Sudah tersedia:

- Tabel `iot_devices`.
- Tabel `iot_attendance_logs`.
- Endpoint `POST /api/iot/attendance`.
- Validasi device token.
- Mapping fingerprint ID ke NIS siswa.
- Pembuatan absensi source `fingerprint`.
- Pembuatan poin absensi otomatis.
- Test feature integrasi fingerprint.
