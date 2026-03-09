# Deskripsi Alur Sistem Aplikasi Rice Log (Untuk UML Use Case)

Dokumen ini menjelaskan alur sistem, aktor, dan skenario utama dalam aplikasi Rice Log untuk memudahkan pembuatan **UML Use Case Diagram**.

---

## 1. Aktor Sistem (Actors)

Dalam aplikasi Rice Log, terdapat dua aktor utama:

1.  **Karyawan (Employee)**: Pengguna yang melakukan absensi, mengajukan cuti, dan menyetorkan hasil pekerjaan (beras).
2.  **Bos (Manager/Admin)**: Pengguna dengan hak akses penuh untuk mengelola data karyawan, memverifikasi setoran, menyetujui cuti, dan mengatur konfigurasi sistem.

---

## 2. Daftar Use Case (Daftar Aksi Pengguna)

### A. Untuk Aktor: Karyawan
- **Login**: Mengakses dashboard pribadi.
- **Absensi Masuk**: Melakukan presensi masuk menggunakan **Face Recognition** dan **GPS Tracking**.
- **Absensi Keluar**: Melakukan presensi keluar (hanya GPS Tracking).
- **Update Profil**: Mengubah data diri atau foto wajah (untuk referensi AI).
- **Pengajuan Cuti**: Mengajukan izin/cuti (dibatasi maksimal 3 hari per bulan).
- **Setor Beras**: Melaporkan hasil pekerjaan dengan mengunggah foto bukti dan input berat (kg).
- **Lihat Statistik Dashboard**: Memantau total gaji, kehadiran, dan setoran bulan berjalan.
- **Lihat Riwayat**: Melihat list absensi dan setoran sebelumnya.
- **Menerima Notifikasi**: Mendapatkan update real-time jika setoran atau cuti telah diverifikasi oleh Bos.

### B. Untuk Aktor: Bos
- **Login**: Mengakses dashboard manajemen.
- **Kelola Karyawan (CRUD)**: Menambah, melihat, mengubah, atau menghapus data karyawan.
- **Verifikasi Setoran**: Meninjau foto bukti setoran dan menyetujui (Verify) atau menolak (Reject) setoran karyawan.
- **Verifikasi Cuti**: Menyetujui atau menolak pengajuan cuti/izin karyawan.
- **Konfigurasi Payroll & Kantor**: Mengatur harga beras per kg, lokasi kantor (koordinat), dan radius toleransi GPS.
- **Monitoring GPS**: Melihat lokasi presensi karyawan pada peta.
- **Laporan Bulanan (Reports)**: Melihat ringkasan kehadiran, total setoran, dan rekap gaji seluruh karyawan.
- **Kelola Stok (Optional)**: Memantau total stok beras yang masuk dari setoran.

---

## 3. Alur Kerja Utama (System Flow)

### 3.1. Alur Presensi (Check-in/Check-out)
1.  Karyawan membuka menu Absensi.
2.  Sistem mendeteksi lokasi GPS dan memverifikasi wajah menggunakan AI (*Face-api.js*).
3.  Jika wajah cocok dan lokasi berada dalam radius kantor, presensi disimpan.
4.  Jika status "Sakit" atau "Izin", karyawan menyertakan alasan tertulis (tanpa verifikasi wajah).
5.  Sistem mengirim notifikasi ke Bos jika ada karyawan yang tidak hadir (Sakit/Izin).

### 3.2. Alur Pengajuan Cuti (Leave Workflow)
1.  Karyawan mengisi formulir cuti (tanggal awal, tanggal akhir, alasan).
2.  Sistem memverifikasi apakah sisa kuota cuti (3 hari/bulan) mencukupi.
3.  Permintaan masuk ke daftar "Pending" pada dashboard Bos.
4.  Bos meninjau dan melakukan **Approve** atau **Reject**.
5.  Status diperbarui di sisi Karyawan dan riwayat kehadiran disesuaikan.

### 3.3. Alur Setor Beras (Deposit Workflow)
1.  Karyawan yang sudah melakukan "Absensi Masuk" dapat mengisi formulir Setor Beras.
2.  Karyawan mengunggah foto fisik beras dan menginput berat (kg).
3.  Sistem menghitung estimasi upah berdasarkan konfigurasi harga saat itu.
4.  Permintaan setoran masuk ke daftar verifikasi Bos.
5.  Bos memvalidasi foto dengan berat yang diinput.
6.  Setelah Bos menekan **Verify**, upah resmi ditambahkan ke saldo bulanan karyawan.

### 3.4. Alur Perhitungan Gaji (Salary Logic)
1.  Sistem memiliki **Monthly Summary** untuk setiap karyawan.
2.  Total gaji bulan berjalan = `Sum(Berat Beras Setoran yang Terverifikasi)` x `Harga per KG (Snapshot saat setor)`.
3.  Data ini diakumulasikan secara otomatis setiap kali ada setoran yang diverifikasi atau perubahan status kehadiran.

---

## 4. Aturan Bisnis Penting (Business Rules)
- **Geofencing**: Karyawan harus berada di lokasi yang ditentukan (toleransi 2km atau sesuai pengaturan).
- **Anti-Fraud**: Foto wajah saat presensi harus asli (deteksi webcam real-time).
- **Verifikasi Manual**: Gaji tidak akan bertambah sebelum Bos melakukan verifikasi foto setoran.
- **Ketergantungan**: Karyawan tidak bisa setor beras jika di hari tersebut belum melakukan absensi masuk.

---

*Catatan: Dokumen ini disusun untuk memudahkan pemetaan elemen dalam Use Case Diagram (Actor, Use Case, dan Relasi <include>/<extend> jika diperlukan).*
