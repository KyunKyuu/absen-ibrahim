# Akun, Username, RBAC, dan Import Google Sheet

## Login

Pengguna dapat login memakai username atau email. Akun hasil import menggunakan username dan password awal dari `IMPORTED_ACCOUNT_DEFAULT_PASSWORD`. Pengguna wajib mengganti password tersebut pada login pertama.

## RBAC

Superadmin dapat membuat role baru, mengubah label dan permission role, membuat akun, serta mengganti role akun. Permission yang tersedia:

- kelola akun dan RBAC;
- kelola data sekolah;
- kelola tagihan dan pembayaran;
- usulkan biaya kelas;
- kelola absensi;
- melakukan check-in;
- memberikan penilaian;
- mengunduh laporan.

Superadmin selalu mempunyai seluruh permission. Sistem menolak perubahan role jika tindakan tersebut akan menghilangkan superadmin aktif terakhir.

## Import Google Sheet

Sheet harus dapat dibaca melalui link dan berasal dari domain `https://docs.google.com`. Batas satu proses import adalah 1.000 baris atau 5 MB.

Header yang dikenali:

| Header | Keterangan |
|---|---|
| `nama` atau `name` | Wajib untuk semua role |
| `username` | Opsional; jika kosong dibuat dari nama |
| `email` | Opsional |
| `nis` | Wajib jika role import adalah siswa |
| `kelas` atau `class` | Wajib untuk siswa dan harus sama dengan nama kelas di sistem |
| `nip` atau `employee_number` | Opsional untuk guru |
| `phone` atau `no_hp` | Opsional untuk orang tua |

Semua baris dalam satu proses mendapat role yang dipilih superadmin. Baris yang gagal tidak membatalkan baris valid lain dan akan ditampilkan dalam ringkasan error.

Versi ini menggunakan link Sheet yang dibagikan, bukan OAuth untuk Sheet privat.
