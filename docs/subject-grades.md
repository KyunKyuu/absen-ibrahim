# Nilai mata pelajaran

Admin menyiapkan tahun ajaran kelas dan semester, lalu menugaskan guru di **Kelas & pengajaran → Penugasan guru** dengan pasangan kelas dan mata pelajaran yang tepat.

Guru membuka **Penilaian → Nilai mata pelajaran → Buat penilaian**, memilih penugasan, semester, jenis (tugas/ulangan harian/UTS/UAS), judul, dan tanggal. Semester wajib sesuai tahun ajaran kelas, dan tanggal harus berada dalam semester tersebut.

Pada detail penilaian, guru mengisi nilai 0–100 (maksimal dua desimal) dan catatan opsional. Daftar siswa memiliki pencarian nama/NIS dan pagination 20 siswa. Simpan sebelum berpindah halaman. Angka 0 adalah nilai sah; kolom kosong tidak menghapus nilai sebelumnya. Menyimpan ulang memperbarui satu nilai per siswa pada penilaian itu.

Server memeriksa kepemilikan penilaian dan penugasan mengajar terkini pada setiap penyimpanan. Wali kelas saja tidak memberikan hak mengisi semua mapel. Guru Bahasa Inggris 11A tidak dapat mengisi Matematika 11A atau Bahasa Inggris 12A tanpa penugasan yang sesuai. Siswa dari kelas lain/nonaktif ditolak dan seluruh batch dibatalkan. Penghapusan penugasan guru tidak menghapus nilai lama, tetapi mencabut hak mengedit. Siswa yang telah pindah kelas tetap terlihat sebagai arsip apabila memiliki nilai yang tercatat.

Admin dengan izin pengelolaan sekolah dan penilaian dapat membaca seluruh penilaian; perubahan nilai tetap harus dilakukan guru pemilik yang memiliki penugasan. Nilai akademik tidak menambah XP dan belum menghitung nilai rapor berbobot karena bobot sekolah belum ditentukan.

Instalasi pada database yang sudah ada:

```sh
php artisan migrate
```

Pengujian hak akses dan penyimpanan:

```sh
php artisan test --filter=SubjectGradesTest
```
