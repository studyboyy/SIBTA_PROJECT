# Panduan Alur Kerja dan Testing Aplikasi SIBTA

Dokumen ini dibuat untuk membantu testing aplikasi SIBTA dari awal sampai akhir. Ikuti urutannya pelan-pelan. Tujuannya bukan hanya memastikan tombol bisa diklik, tetapi memastikan alur TA masuk akal untuk role Admin, Mahasiswa, Dosen, Kaprodi, dan Pimpinan.

## 1. Persiapan Sebelum Testing

### 1.1 Jalankan database seed

Jika ingin data benar-benar bersih dan jumlah datanya sesuai seed terbaru, jalankan:

```bash
php artisan migrate:fresh --seed
```

Perintah ini akan menghapus data lama, menjalankan semua migrasi, lalu mengisi data demo:

- 2 program studi: Teknik Informatika dan Sistem Informasi.
- 2 kaprodi, masing-masing mengikuti program studi.
- 10 dosen.
- 30 mahasiswa, masing-masing berbeda.
- Semua mahasiswa seed sudah punya dosen pembimbing aktif.

Jika tidak ingin reset database, cukup jalankan:

```bash
php artisan db:seed
```

Catatan penting: `migrate:fresh --seed` lebih cocok untuk testing dari nol karena datanya bersih.

### 1.2 Jalankan aplikasi

Jika memakai Laravel Herd, buka domain lokal project seperti biasa.

Jika menjalankan manual, biasanya gunakan:

```bash
php artisan serve
npm run dev
```

Jika gambar/avatar/file upload tidak tampil, pastikan storage link sudah ada:

```bash
php artisan storage:link
```

## 2. Akun Login Testing

Gunakan akun berikut agar tidak bingung saat berpindah role.

| Role | Email | Password | Catatan |
| --- | --- | --- | --- |
| Admin | `admin@sibta.test` | `Admin123!` | Kelola data master, jadwal sidang, penguji, laporan |
| Pimpinan | `pimpinan@sibta.test` | `Pimpinan123!` | Monitoring dan laporan tingkat pimpinan |
| Kaprodi TI | `kaprodi.ti@sibta.test` | `Kaprodi123!` | Kaprodi Teknik Informatika |
| Kaprodi SI | `kaprodi.si@sibta.test` | `Kaprodi123!` | Kaprodi Sistem Informasi |
| Dosen 1 | `dosen.budi@sibta.test` | `Dosen123!` | Pembimbing beberapa mahasiswa seed |
| Dosen 2 | `dosen.siti@sibta.test` | `Dosen123!` | Pembimbing beberapa mahasiswa seed |
| Mahasiswa TI 1 | `mhs.ti.001@sibta.test` | `Mahasiswa123!` | Andi Prasetyo |
| Mahasiswa SI 1 | `mhs.si.001@sibta.test` | `Mahasiswa123!` | Putri Aulia |

Untuk mahasiswa lain, pola emailnya:

- Teknik Informatika: `mhs.ti.001@sibta.test` sampai `mhs.ti.015@sibta.test`.
- Sistem Informasi: `mhs.si.001@sibta.test` sampai `mhs.si.015@sibta.test`.
- Semua password mahasiswa: `Mahasiswa123!`.

## 3. Peta Alur Besar Aplikasi

Alur utama aplikasi SIBTA adalah:

1. Admin memastikan data prodi, kaprodi, dosen, mahasiswa, dan pembimbing tersedia.
2. Mahasiswa mengajukan judul TA.
3. Dosen pembimbing mereview pengajuan judul.
4. Mahasiswa mengunggah dokumen wajib, yaitu proposal dan skripsi.
5. Dosen mereview dokumen.
6. Mahasiswa mengajukan sidang jika dokumen wajib sudah disetujui.
7. Dosen memberi ACC kelayakan sidang.
8. Kaprodi memberi approval sidang.
9. Admin membuat batch jadwal sidang dan menjadwalkan mahasiswa.
10. Admin menandai hasil sidang menjadi `Selesai`, `Lulus`, atau `Tidak Lulus`.
11. Status TA mahasiswa berubah otomatis mengikuti kondisi terbaru.

Status TA yang benar:

| Kondisi Mahasiswa | Status TA |
| --- | --- |
| Belum punya pembimbing | Pending |
| Sudah punya pembimbing, tapi sidang belum final | Proses |
| Sidang sudah final dengan status `selesai` atau `lulus` | Selesai |

## 4. Alur Testing Admin

Login sebagai:

```text
admin@sibta.test
Admin123!
```

### 4.1 Cek dashboard admin

1. Login sebagai admin.
2. Pastikan masuk ke Dashboard Admin.
3. Cek apakah ringkasan data tampil.
4. Pastikan sidebar admin muncul.

Hasil yang diharapkan:

- Admin bisa melihat menu Data Dosen, Kelola Kaprodi, Program Studi, Data Mahasiswa, Kelola Bimbingan, Jadwal Sidang & Penguji, Pengelolaan Dokumen, Laporan, dan User Admin.
- Tidak muncul menu khusus mahasiswa atau dosen.

### 4.2 Cek Program Studi

Menu: `Program Studi`

Langkah:

1. Buka menu Program Studi.
2. Pastikan hanya ada Teknik Informatika dan Sistem Informasi.
3. Pastikan masing-masing punya kaprodi.

Hasil yang diharapkan:

- Teknik Informatika memiliki Kaprodi TI.
- Sistem Informasi memiliki Kaprodi SI.
- Tidak ada prodi lain dari seed baru.

### 4.3 Cek Kelola Kaprodi

Menu: `Kelola Kaprodi`

Langkah:

1. Buka menu Kelola Kaprodi.
2. Pastikan ada 2 akun kaprodi.
3. Coba cari nama kaprodi atau email kaprodi.

Hasil yang diharapkan:

- Ada `kaprodi.ti@sibta.test`.
- Ada `kaprodi.si@sibta.test`.
- Keduanya punya role kaprodi.

### 4.4 Cek Data Dosen

Menu: `Data Dosen`

Langkah:

1. Buka menu Data Dosen.
2. Pastikan ada 10 dosen dari seed.
3. Coba search nama dosen, misalnya `Budi`.
4. Coba buka edit salah satu dosen tanpa menyimpan perubahan.

Hasil yang diharapkan:

- Dosen tampil lengkap dengan nama, NIDN, jabatan, telepon, kuota.
- Pencarian berjalan.
- Form edit terbuka tanpa error.

### 4.5 Cek Data Mahasiswa dan Status TA

Menu: `Data Mahasiswa`

Langkah:

1. Buka menu Data Mahasiswa.
2. Pastikan ada 30 mahasiswa seed.
3. Search `Andi Prasetyo`.
4. Lihat status TA mahasiswa tersebut.

Hasil yang diharapkan:

- Mahasiswa seed tampil.
- Status TA mahasiswa seed adalah `Proses`, karena seed sudah memberi dosen pembimbing aktif.
- Program studi tampil sesuai data, hanya Teknik Informatika atau Sistem Informasi.

Catatan:

Jika ingin mengetes mahasiswa tanpa pembimbing, buat mahasiswa baru dari admin tetapi jangan beri pembimbing. Setelah itu login sebagai mahasiswa tersebut. Beberapa menu mahasiswa harus terkunci sampai pembimbing ditetapkan.

### 4.6 Cek Kelola Bimbingan

Menu: `Kelola Bimbingan`

Langkah:

1. Buka menu Kelola Bimbingan.
2. Pastikan mahasiswa memiliki pembimbing.
3. Coba cari `Andi Prasetyo`.
4. Pastikan dosen pembimbingnya tampil.

Hasil yang diharapkan:

- Setiap mahasiswa seed punya satu pembimbing aktif.
- Jika pembimbing diganti, status TA tetap `Proses`.
- Mahasiswa tidak boleh punya lebih dari satu pembimbing aktif.

## 5. Alur Testing Mahasiswa - Pengajuan Judul

Login sebagai:

```text
mhs.ti.001@sibta.test
Mahasiswa123!
```

Mahasiswa ini bernama Andi Prasetyo.

### 5.1 Cek dashboard mahasiswa

Langkah:

1. Login sebagai mahasiswa.
2. Pastikan masuk ke Dashboard Mahasiswa.
3. Lihat ringkasan dokumen, bimbingan, dan progres TA.

Hasil yang diharapkan:

- Mahasiswa bisa melihat dashboard.
- Menu mahasiswa terbuka karena mahasiswa seed sudah punya pembimbing.
- Status TA awal adalah `Proses`.

### 5.2 Ajukan judul TA

Menu: `Form Pengajuan`

Tab: `Pengajuan Judul`

Langkah:

1. Buka menu Form Pengajuan.
2. Pastikan tab Pengajuan Judul aktif.
3. Isi judul, contoh: `Sistem Informasi Monitoring Bimbingan Tugas Akhir Berbasis Web`.
4. Isi deskripsi singkat.
5. Klik simpan atau ajukan.
6. Buka tab Riwayat Pengajuan Judul.

Hasil yang diharapkan:

- Pengajuan judul masuk ke riwayat.
- Status awal pengajuan adalah `pending`.
- Mahasiswa belum bisa mengajukan judul baru jika sudah ada judul yang disetujui.

### 5.3 Testing revisi judul

Langkah ini dilakukan jika dosen memberi status `revisi` atau `rejected`.

Langkah:

1. Login kembali sebagai mahasiswa.
2. Buka Form Pengajuan.
3. Buka tab Riwayat Pengajuan Judul.
4. Cari judul yang statusnya revisi atau rejected.
5. Klik aksi revisi.
6. Perbaiki judul atau deskripsi.
7. Kirim revisi.

Hasil yang diharapkan:

- Status judul kembali menjadi `pending`.
- Revisi ke bertambah.
- Dosen bisa melihat pengajuan revisi tersebut.

## 6. Alur Testing Dosen - Review Judul

Login sebagai dosen pembimbing mahasiswa yang diuji.

Untuk Andi Prasetyo, gunakan:

```text
dosen.budi@sibta.test
Dosen123!
```

### 6.1 Cek dashboard dosen

Langkah:

1. Login sebagai dosen.
2. Pastikan masuk ke Dashboard Dosen.
3. Cek ringkasan mahasiswa bimbingan, judul, dokumen, dan sidang.

Hasil yang diharapkan:

- Dosen hanya melihat mahasiswa bimbingannya.
- Dosen tidak melihat mahasiswa yang bukan bimbingannya.

### 6.2 Review pengajuan judul

Menu: `Review Pengajuan Judul`

Langkah:

1. Buka menu Review Pengajuan Judul.
2. Cari nama mahasiswa atau judul yang diajukan.
3. Klik edit status atau aksi review.
4. Coba alur `approved`.
5. Untuk testing tambahan, coba buat pengajuan lain lalu beri status `revisi` dengan catatan.

Hasil yang diharapkan:

- Judul bisa disetujui oleh dosen.
- Kaprodi tidak perlu approve judul.
- Jika status `revisi` atau `rejected`, catatan wajib diisi.
- Jika status `approved`, mahasiswa tidak bisa mengajukan judul baru lagi.

## 7. Alur Testing Pengajuan Dosen Pembimbing

Menu mahasiswa: `Form Pengajuan`

Tab: `Pengajuan Dosen`

Alur ini dipakai saat mahasiswa ingin mengganti dosen pembimbing.

### 7.1 Mahasiswa mengajukan ganti dosen

Login sebagai:

```text
mhs.ti.001@sibta.test
Mahasiswa123!
```

Langkah:

1. Buka Form Pengajuan.
2. Pilih tab Pengajuan Dosen.
3. Pilih dosen baru yang berbeda dari pembimbing aktif.
4. Isi alasan, contoh: `Topik penelitian lebih sesuai dengan bidang dosen tujuan.`
5. Kirim pengajuan.

Hasil yang diharapkan:

- Pengajuan pembimbing masuk dengan status `pending`.
- Mahasiswa tidak bisa mengajukan dosen yang sama dengan pembimbing aktif.
- Mahasiswa tidak bisa membuat pengajuan baru jika masih ada pengajuan pembimbing yang pending.

### 7.2 Kaprodi approve pengajuan dosen

Login sebagai Kaprodi sesuai prodi mahasiswa.

Untuk mahasiswa TI:

```text
kaprodi.ti@sibta.test
Kaprodi123!
```

Menu: `Pengajuan Pembimbing`

Langkah:

1. Buka menu Pengajuan Pembimbing.
2. Cari pengajuan mahasiswa.
3. Klik setujui.
4. Logout.
5. Login lagi sebagai mahasiswa.
6. Cek pembimbing aktif di dashboard atau Form Pengajuan.

Hasil yang diharapkan:

- Pengajuan berubah menjadi `approved`.
- Pembimbing mahasiswa berubah ke dosen baru.
- Mahasiswa tidak lagi terhubung ke dosen pembimbing sebelumnya.
- Dosen lama tidak bisa melihat mahasiswa tersebut di daftar bimbingannya.
- Dosen baru bisa melihat mahasiswa tersebut.

## 8. Alur Testing Dokumen TA

Dokumen wajib sebelum sidang:

- Proposal.
- Skripsi atau laporan akhir.

File yang bisa dipakai untuk testing:

- PDF.
- DOC.
- DOCX.
- Maksimal 5 MB untuk upload mahasiswa.

### 8.1 Mahasiswa upload dokumen

Login sebagai mahasiswa:

```text
mhs.ti.001@sibta.test
Mahasiswa123!
```

Menu: `Dokumen Saya`

Langkah:

1. Buka menu Dokumen Saya.
2. Upload dokumen dengan jenis `Dokumen Proposal Skripsi`.
3. Pilih file PDF atau DOCX.
4. Simpan.
5. Upload dokumen kedua dengan jenis `Dokumen Skripsi (Laporan Akhir)`.
6. Simpan.

Hasil yang diharapkan:

- Kedua dokumen tampil di daftar.
- Status awal dokumen adalah `pending`.
- Checklist sidang belum terpenuhi sampai dosen menyetujui dokumen.

### 8.2 Dosen review dokumen

Login sebagai dosen pembimbing:

```text
dosen.budi@sibta.test
Dosen123!
```

Menu: `Review Dokumen`

Langkah:

1. Buka menu Review Dokumen.
2. Cari dokumen mahasiswa.
3. Approve dokumen proposal.
4. Approve dokumen skripsi.

Hasil yang diharapkan:

- Status dokumen berubah menjadi `disetujui`.
- Mahasiswa melihat checklist proposal dan skripsi terpenuhi.
- Mahasiswa baru bisa lanjut pengajuan sidang setelah dua dokumen wajib disetujui.

### 8.3 Testing revisi dokumen

Langkah:

1. Dari akun dosen, pilih salah satu dokumen.
2. Isi catatan revisi.
3. Klik minta revisi.
4. Login sebagai mahasiswa.
5. Buka Revisi Saya atau Dokumen Saya.
6. Upload file revisi.
7. Login lagi sebagai dosen.
8. Approve dokumen hasil revisi.

Hasil yang diharapkan:

- Mahasiswa bisa melihat catatan revisi.
- Setelah mahasiswa mengirim revisi, status dokumen kembali `pending`.
- Setelah dosen approve, status dokumen menjadi `disetujui`.

## 9. Alur Testing Bimbingan

### 9.1 Dosen membuat jadwal bimbingan

Login sebagai dosen:

```text
dosen.budi@sibta.test
Dosen123!
```

Menu: `Penjadwalan Bimbingan`

Langkah:

1. Buka menu Penjadwalan Bimbingan.
2. Pilih tanggal.
3. Isi jam.
4. Pilih mode `Offline`.
5. Isi lokasi, contoh: `Ruang Dosen 2`.
6. Isi agenda.
7. Simpan jadwal.

Hasil yang diharapkan:

- Jadwal dibuat untuk mahasiswa bimbingan dosen tersebut.
- Mahasiswa bisa melihat jadwal di menu Jadwal Saya atau Bimbingan.

### 9.2 Testing bimbingan online

Langkah:

1. Dari akun dosen, buka Penjadwalan Bimbingan.
2. Pilih mode `Online`.
3. Isi link meeting, contoh: `https://meet.google.com/abc-defg-hij`.
4. Simpan.
5. Login sebagai mahasiswa.
6. Buka Jadwal Saya.

Hasil yang diharapkan:

- Link meeting tersimpan sebagai link online.
- Mahasiswa bisa melihat atau membuka link meeting.
- Field online tidak tersimpan sebagai lokasi offline.

### 9.3 Mahasiswa konfirmasi bimbingan

Login sebagai mahasiswa:

```text
mhs.ti.001@sibta.test
Mahasiswa123!
```

Menu: `Bimbingan`

Langkah:

1. Buka menu Bimbingan.
2. Cari jadwal bimbingan.
3. Konfirmasi hadir jika tersedia.
4. Isi catatan hasil bimbingan.
5. Simpan.

Hasil yang diharapkan:

- Konfirmasi mahasiswa berubah dari pending menjadi hadir.
- Catatan hasil bimbingan tersimpan.
- Dosen bisa membaca catatan tersebut.

## 10. Alur Testing Pengajuan Sidang

Syarat sebelum mahasiswa bisa mengajukan sidang:

- Mahasiswa punya pembimbing aktif.
- Dokumen proposal sudah disetujui dosen.
- Dokumen skripsi sudah disetujui dosen.
- Mahasiswa belum punya jadwal sidang.

### 10.1 Mahasiswa cek checklist sidang

Login sebagai mahasiswa:

```text
mhs.ti.001@sibta.test
Mahasiswa123!
```

Menu: `Checklist Sidang`

Langkah:

1. Buka menu Checklist Sidang.
2. Lihat status proposal.
3. Lihat status skripsi.
4. Pastikan keduanya sudah terpenuhi.

Hasil yang diharapkan:

- Jika proposal dan skripsi sudah disetujui, checklist siap.
- Jika salah satu belum disetujui, mahasiswa belum bisa lanjut sidang.

### 10.2 Mahasiswa mengajukan sidang

Menu: `Pengajuan Sidang`

Langkah:

1. Buka menu Pengajuan Sidang.
2. Pastikan semua syarat terpenuhi.
3. Isi catatan opsional.
4. Klik Ajukan Sidang.

Hasil yang diharapkan:

- Pengajuan sidang masuk.
- Status dosen `pending`.
- Status kaprodi `pending`.
- Status admin `pending`.
- Mahasiswa tidak bisa submit ulang saat pengajuan sedang diproses.

## 11. Alur Testing ACC Sidang oleh Dosen

Login sebagai dosen pembimbing:

```text
dosen.budi@sibta.test
Dosen123!
```

Menu: `Kelayakan Sidang`

Langkah:

1. Buka menu Kelayakan Sidang.
2. Cari pengajuan sidang mahasiswa.
3. Pastikan dokumen wajib sudah lengkap.
4. Klik approve atau ACC.

Hasil yang diharapkan:

- Status dosen berubah menjadi `approved`.
- Jika dokumen wajib belum lengkap, dosen tidak bisa approve.
- Jika memilih `revisi` atau `rejected`, catatan wajib diisi.

Catatan penting:

Halaman lama `Kontrol Bimbingan` juga harus mengikuti aturan yang sama. Dosen tidak boleh bisa approve sidang dari halaman lama jika dokumen wajib belum lengkap.

## 12. Alur Testing Approval Sidang oleh Kaprodi

Login sebagai kaprodi sesuai prodi mahasiswa.

Untuk mahasiswa TI:

```text
kaprodi.ti@sibta.test
Kaprodi123!
```

Menu: `Approval Sidang`

Langkah:

1. Buka menu Approval Sidang.
2. Cari pengajuan sidang mahasiswa.
3. Pastikan status dosen sudah `approved`.
4. Klik approve.

Hasil yang diharapkan:

- Status kaprodi berubah menjadi `approved`.
- Kaprodi tidak bisa approve jika dosen belum approve.
- Kaprodi TI hanya menangani mahasiswa TI.
- Kaprodi SI hanya menangani mahasiswa SI.

## 13. Alur Testing Jadwal Sidang dan Penguji oleh Admin

Login sebagai admin:

```text
admin@sibta.test
Admin123!
```

Menu: `Jadwal Sidang & Penguji`

Halaman ini memiliki 3 tab:

- Jadwal & Batch.
- Approval Pengajuan.
- Daftar Mahasiswa.

### 13.1 Buat batch jadwal sidang

Tab: `Jadwal & Batch`

Langkah:

1. Buka tab Jadwal & Batch.
2. Isi gelombang, atau kosongkan agar otomatis.
3. Isi kuota mahasiswa.
4. Isi tanggal sidang.
5. Isi jam mulai dan jam selesai.
6. Isi ruangan.
7. Pilih ketua sidang.
8. Pilih penguji 1.
9. Pilih penguji 2.
10. Simpan jadwal.

Hasil yang diharapkan:

- Batch sidang berhasil dibuat.
- Batch berlaku untuk semua prodi.
- Tidak ada pilihan prodi saat membuat jadwal sidang.
- Ketua, penguji 1, dan penguji 2 tidak boleh orang yang sama.

### 13.2 Approve pengajuan sidang dan jadwalkan mahasiswa

Tab: `Approval Pengajuan`

Langkah:

1. Buka tab Approval Pengajuan.
2. Cari pengajuan mahasiswa yang status dosen dan kaprodi sudah approved.
3. Klik Approve & Jadwalkan.

Hasil yang diharapkan:

- Status admin berubah menjadi `approved`.
- Mahasiswa otomatis masuk ke batch yang masih punya kuota.
- Gelombang terisi otomatis dari batch.
- Jika belum ada batch, admin tidak bisa approve dan harus membuat batch dulu.

### 13.3 Cek daftar mahasiswa

Tab: `Daftar Mahasiswa`

Langkah:

1. Buka tab Daftar Mahasiswa.
2. Cari nama mahasiswa.
3. Gunakan filter status `Layak Sidang` atau `Belum Layak`.
4. Gunakan filter prodi.

Hasil yang diharapkan:

- Semua mahasiswa yang sudah punya pembimbing tampil.
- Kolom nama, NIM, program studi, pembimbing, kaprodi, dan status tampil.
- Mahasiswa yang sudah di-ACC dosen tampil sebagai Layak Sidang.

### 13.4 Tandai hasil sidang

Tab: `Jadwal & Batch`

Langkah:

1. Cari batch sidang yang berisi mahasiswa.
2. Pada daftar mahasiswa di batch, klik `Lulus` atau `Selesai`.
3. Buka Data Mahasiswa.
4. Cari mahasiswa tersebut.

Hasil yang diharapkan:

- Status sidang berubah menjadi `lulus` atau `selesai`.
- Status TA mahasiswa berubah menjadi `Selesai`.
- Jika status sidang `pending` atau `tidak_lulus`, status TA tidak menjadi `Selesai`.

## 14. Alur Testing Pimpinan

Login sebagai:

```text
pimpinan@sibta.test
Pimpinan123!
```

Langkah:

1. Login sebagai pimpinan.
2. Buka dashboard.
3. Buka monitoring mahasiswa.
4. Buka laporan.
5. Cek apakah data lintas prodi tampil.

Hasil yang diharapkan:

- Pimpinan dapat melihat ringkasan data umum.
- Pimpinan tidak diperlakukan seperti kaprodi yang hanya punya satu prodi.
- Laporan dapat dibuka.

## 15. Skenario Error yang Wajib Dicoba

Skenario ini penting karena dosen penguji tugas akhir biasanya suka mencoba alur yang tidak ideal.

### 15.1 Mahasiswa upload dokumen salah format

Langkah:

1. Login mahasiswa.
2. Buka Dokumen Saya.
3. Upload file selain PDF, DOC, atau DOCX.

Hasil yang diharapkan:

- Sistem menolak file.
- Muncul pesan validasi.
- Data tidak tersimpan.

### 15.2 Dosen menolak dokumen tanpa catatan

Langkah:

1. Login dosen.
2. Buka Review Dokumen.
3. Pilih dokumen.
4. Klik tolak tanpa mengisi catatan.

Hasil yang diharapkan:

- Sistem menolak aksi.
- Muncul pesan bahwa catatan wajib diisi.

### 15.3 Mahasiswa ajukan sidang sebelum dokumen lengkap

Langkah:

1. Login mahasiswa yang dokumennya belum lengkap atau belum disetujui.
2. Buka Pengajuan Sidang.
3. Coba ajukan sidang.

Hasil yang diharapkan:

- Tombol pengajuan tidak tersedia atau sistem menolak.
- Pesan menjelaskan dokumen wajib belum terpenuhi.

### 15.4 Dosen ACC sidang sebelum dokumen lengkap

Langkah:

1. Buat pengajuan sidang mahasiswa yang dokumennya belum lengkap.
2. Login dosen.
3. Buka Kelayakan Sidang atau Kontrol Bimbingan.
4. Coba approve.

Hasil yang diharapkan:

- Sistem menolak approval.
- Status dosen tetap `pending`.

### 15.5 Admin approve sidang sebelum kaprodi approve

Langkah:

1. Buat pengajuan sidang yang baru di-ACC dosen.
2. Login admin.
3. Buka Approval Pengajuan di Jadwal Sidang.
4. Coba approve.

Hasil yang diharapkan:

- Admin belum bisa menjadwalkan.
- Sistem menunggu approval kaprodi.

### 15.6 Mahasiswa submit ulang sidang saat sedang diproses

Langkah:

1. Mahasiswa sudah mengajukan sidang.
2. Dosen atau kaprodi sudah approve.
3. Login mahasiswa.
4. Buka Pengajuan Sidang.
5. Coba submit ulang.

Hasil yang diharapkan:

- Mahasiswa tidak bisa submit ulang.
- Status approval dosen dan kaprodi tidak kembali ke pending.

## 16. Alur Testing Cepat 30 Menit

Jika waktu testing terbatas, pakai alur cepat ini.

1. Jalankan `php artisan migrate:fresh --seed`.
2. Login admin, cek Data Mahasiswa dan pastikan ada 30 mahasiswa.
3. Login mahasiswa `mhs.ti.001@sibta.test`, ajukan judul.
4. Login dosen `dosen.budi@sibta.test`, approve judul.
5. Login mahasiswa, upload Proposal dan Skripsi.
6. Login dosen, approve kedua dokumen.
7. Login mahasiswa, ajukan sidang.
8. Login dosen, approve Kelayakan Sidang.
9. Login kaprodi TI, approve sidang.
10. Login admin, buat batch sidang.
11. Admin approve pengajuan sidang dan jadwalkan.
12. Admin tandai sidang `Lulus`.
13. Admin buka Data Mahasiswa dan pastikan status TA mahasiswa menjadi `Selesai`.

## 17. Checklist Akhir Sebelum Demo

Gunakan checklist ini sebelum presentasi atau sidang tugas akhir.

- Login semua role berhasil.
- Admin melihat 30 mahasiswa, 10 dosen, 2 prodi, 2 kaprodi.
- Mahasiswa bisa ajukan judul.
- Dosen bisa approve judul.
- Mahasiswa bisa upload proposal dan skripsi.
- Dosen bisa approve dokumen.
- Mahasiswa bisa ajukan sidang setelah dokumen lengkap.
- Dosen tidak bisa ACC sidang jika dokumen wajib belum lengkap.
- Kaprodi hanya approve sidang sesuai prodi.
- Admin bisa membuat batch tanpa memilih prodi.
- Admin bisa menjadwalkan mahasiswa ke batch.
- Status TA berubah `Proses` setelah ada pembimbing.
- Status TA berubah `Selesai` setelah sidang berstatus `selesai` atau `lulus`.
- Laporan dan monitoring dapat dibuka tanpa error.

## 18. Catatan Urutan Role Saat Testing Manual

Agar tidak bolak-balik bingung, pakai urutan login ini:

1. Admin: cek data master dan jadwal.
2. Mahasiswa: ajukan judul, upload dokumen, ajukan sidang.
3. Dosen: approve judul, approve dokumen, approve kelayakan sidang.
4. Kaprodi: approve pengajuan pembimbing atau sidang.
5. Admin: jadwalkan sidang dan tandai hasil sidang.
6. Pimpinan: cek dashboard dan laporan.

Urutan ini mengikuti alur kerja yang paling masuk akal untuk demo aplikasi SIBTA.
