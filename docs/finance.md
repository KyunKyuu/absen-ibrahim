# Modul Keuangan Siswa

## Hak akses

- **TU/Admin:** membuat jenis tagihan, menerbitkan tagihan satu kelas, memverifikasi usulan wali kelas, dan mencatat pembayaran.
- **Wali kelas:** mengusulkan biaya dinamis hanya untuk kelas yang menjadi tanggung jawabnya.
- **Siswa/orang tua:** tidak memiliki akses ke menu, nominal, tagihan, pembayaran, maupun tunggakan.
- **Wali kelas:** hanya melihat formulir dan status usulannya sendiri; tidak melihat tagihan atau pembayaran siswa.

Role `admin` pada implementasi saat ini berfungsi sebagai superadmin sekolah.

## Tagihan sekolah

TU dapat membuat master seperti SPP, uang bangunan, daftar ulang, atau jenis lain. Tagihan diterbitkan ke seluruh siswa aktif pada kelas dan periode yang dipilih. Kombinasi siswa, jenis tagihan, dan periode dibuat idempoten agar klik ulang tidak menggandakan tagihan.

Pembayaran dicatat manual per tagihan. Pembayaran sebagian menghasilkan status `partial`; pembayaran sebesar sisa tagihan menghasilkan status `paid`; pembayaran berlebih ditolak.

## Usulan biaya wali kelas

Usulan belum menjadi tagihan selama statusnya `pending`. TU/Admin dapat:

- menyetujui, lalu sistem menerbitkan tagihan untuk seluruh siswa aktif di kelas; atau
- menolak dengan alasan, tanpa menerbitkan tagihan.

Mode nominal:

- `per_student`: nominal usulan dikenakan kepada setiap siswa;
- `collective`: nominal usulan adalah target total kelas dan dibagi ke seluruh siswa. Sisa pembulatan rupiah didistribusikan mulai dari siswa dengan ID terkecil sehingga jumlah seluruh tagihan selalu tepat sama dengan target.

## Naik atau pindah kelas

Perpindahan menyimpan histori kelas lama dan kelas baru. Tagihan lama yang belum lunas tidak disalin agar utang tidak ganda; record yang sama tetap aktif dan ditandai sebagai `tunggakan`. Karena tagihan menyimpan snapshot kelas saat diterbitkan, histori asal tunggakan tetap terlihat setelah siswa berpindah kelas.

## Batasan versi awal

- Belum ada nomor kuitansi dan cetak kuitansi/PDF.
- Belum ada pembatalan pembayaran dengan approval dan audit ledger.
- Belum ada rekonsiliasi bank/payment gateway.
- Belum ada diskon, beasiswa, denda, atau cicilan terjadwal.
- Belum ada pengingat otomatis jatuh tempo.
- Promosi kelas masih dilakukan satu siswa per tindakan, belum massal.
