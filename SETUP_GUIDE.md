# 🌾 Rice Log - Sistem Absensi Karyawan Pabrik Beras

Sistem manajemen absensi karyawan pabrik beras yang modern dengan fitur **Face Recognition**, **Google Maps Integration**, dan **Real-time Notifications**.

## ✨ Fitur Utama

### Untuk Karyawan:

- ✅ **Dashboard Personal** - Rekapitulasi absensi, cuti, dan penghasilan
- 📸 **Presensi dengan Face Recognition** - Menggunakan AI face detection (face-api.js)
- 🗺️ **Tracking Lokasi GPS** - Integrasi Google Maps untuk tracking posisi
- 📋 **Pengajuan Cuti** - Dengan limit 3 hari/bulan
- 🌾 **Setor Beras** - Catat penyetoran beras dan hitung gaji
- 📱 **Notifikasi Real-time** - Notifikasi status pengajuan dan verifikasi
- 🔍 **Riwayat Lengkap** - Lihat semua aktivitas absensi dan setor

### Untuk Bos/Atasan:

- 📊 **Dashboard Analytics** - Pendapatan bulanan dan statistik karyawan
- 👥 **Manajemen Karyawan** - Tambah, ubah status karyawan
- ✔️ **Persetujuan Cuti** - Approve/reject pengajuan cuti dengan notifikasi
- 🔐 **Verifikasi Setor** - Verifikasi setor beras karyawan
- ⚙️ **Pengaturan Gaji** - Atur harga beras per kg
- 📍 **Monitoring Lokasi** - Lihat lokasi karyawan saat absen
- 📈 **Report Bulanan** - Rekapitulasi per karyawan per bulan

## 🛠️ Tech Stack

- **Backend:** Laravel 11
- **Frontend:** Blade Template, Tailwind CSS
- **Database:** MySQL/MariaDB
- **Face Recognition:** face-api.js v0.8.5
- **Maps:** Google Maps API
- **UI Library:** SweetAlert2, Font Awesome

## 📋 Prasyarat

- PHP 8.2+
- Composer
- MySQL 5.7+
- Node.js (untuk assets)
- Google Maps API Key
- Face-api.js CDN (online)

## 🚀 Instalasi & Setup

### 1. Clone Repository

```bash
git clone <repository-url>
cd rice-log
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rice_log
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Setup Google Maps API

1. Buka [Google Cloud Console](https://console.cloud.google.com)
2. Buat project baru
3. Enable Maps JavaScript API
4. Buat API Key
5. Edit file `resources/views/employee/absence/form.blade.php`
6. Ganti `YOUR_GOOGLE_MAPS_API_KEY` dengan API key Anda

### 6. Migrasi Database

```bash
php artisan migrate
php artisan db:seed
```

### 7. Build Assets

```bash
npm run build
```

### 8. Jalankan Server

```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

## 👤 Demo Login

Setelah menjalankan seeder, gunakan akun berikut:

**Akun Bos:**

- Email: `bos@ricemail.com`
- Password: `password`

**Akun Karyawan:**

- Silakan cek database atau check email yang di-generate oleh seeder (default password: `password`)

## 📁 Struktur Folder

```
rice-log/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── EmployeeDashboardController.php
│   │   │   ├── AbsenceController.php
│   │   │   ├── LeaveSubmissionController.php
│   │   │   ├── DepositController.php
│   │   │   ├── BossDashboardController.php
│   │   │   └── ... (controllers lainnya)
│   │   └── Middleware/
│   │       ├── EnsureUserIsEmployee.php
│   │       └── EnsureUserIsBoss.php
│   └── Models/
│       ├── User.php
│       ├── Absence.php
│       ├── LeaveSubmission.php
│       ├── Deposit.php
│       └── ... (models lainnya)
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── resources/
│   └── views/
│       ├── layouts/
│       ├── employee/
│       ├── boss/
│       ├── auth/
│       └── components/
├── routes/
│   ├── web.php
│   └── api.php
└── ...
```

## 🔌 API Endpoints

### Notifications (Authenticated)

```
GET  /api/notifications              - Dapatkan notifikasi tidak terbaca
POST /api/notifications/{id}/read    - Tandai notifikasi sebagai sudah dibaca
```

## 🗄️ Database Schema

### Users Table

```sql
- id
- name
- email
- password
- role (karyawan/bos)
- date_of_birth
- phone
- address
- latitude
- longitude
- status (active/inactive)
- last_presence_at
- timestamps
```

### Absences Table

```sql
- id
- user_id
- type (masuk/keluar)
- status (hadir/sakit/izin)
- description
- face_image
- latitude
- longitude
- distance_from_office
- checked_at
- timestamps
```

### LeaveSubmissions Table

```sql
- id
- user_id
- start_date
- end_date
- reason
- status (pending/approved/rejected)
- approved_by
- rejection_reason
- approved_at
- timestamps
```

### Deposits Table

```sql
- id
- user_id
- weight (dalam kg)
- price_per_kg
- total_price
- photo
- notes
- status (pending/verified/rejected)
- verified_by
- verified_at
- timestamps
```

### PayrollSettings Table

```sql
- id
- price_per_kg
- office_latitude
- office_longitude
- max_distance_allowed
- leave_days_per_month
- min_deposit_per_week
- timestamps
```

### MonthlySummaries Table

```sql
- id
- user_id
- year
- month
- days_present
- days_sick
- days_leave
- leave_approved
- total_kg_deposited
- total_salary
- status (active/inactive)
- timestamps
```

## 🎯 Fitur Workflow

### Alur Presensi Karyawan

1. Karyawan membuka aplikasi di `/employee/dashboard`
2. Klik "Presensi Masuk"
3. Sistem mengakses GPS untuk mendapatkan lokasi
4. Google Maps menampilkan posisi karyawan dan kantor
5. Karyawan memilih status: Hadir/Sakit/Izin
6. Jika "Hadir", kamera aktif dan face-api mendeteksi wajah
7. Karyawan klik "Ambil Foto" untuk capture wajah
8. Sistem mengirim data ke server
9. Notifikasi sukses tampil

### Alur Pengajuan Cuti

1. Karyawan klik "Pengajuan Cuti"
2. Pilih tanggal mulai dan selesai (max 3 hari/bulan)
3. Isi alasan cuti
4. Submit pengajuan
5. Notifikasi dikirim ke Bos
6. Bos review dan approve/reject
7. Karyawan menerima notifikasi hasil

### Alur Setor Beras

1. Karyawan klik "Setor Beras"
2. Input berat beras (kg)
3. Upload foto beras
4. Sistem otomatis hitung gaji (berat × harga/kg)
5. Submit setor
6. Notifikasi ke Bos untuk verifikasi
7. Bos verify dan karyawan bisa lihat gaji

## 🔒 Security Features

- ✅ Middleware role-based access control (karyawan/bos)
- ✅ CSRF Protection
- ✅ Password hashing dengan bcrypt
- ✅ Sanctum untuk API authentication
- ✅ Input validation di setiap endpoint
- ✅ Face image upload dengan file type validation

## 🎨 Design Features

- 🌈 Modern Gradient UI dengan Tailwind CSS
- 📱 Responsive Design (mobile-first)
- 🎭 Smooth Animations dan Transitions
- 🔔 SweetAlert2 untuk notifikasi interaktif
- 🏆 Intuitive Navigation dengan Sidebar
- 📊 Data Visualization dengan Statistics Cards

## 📸 Screenshot Fitur

### Employee Dashboard

- Summary stats (Hadir, Sakit, Izin, Cuti)
- Quick action buttons
- Recent activities
- Unread notifications

### Absence Form

- Interactive Google Maps
- Real-time face detection
- Distance warning jika >2km
- Status selection (Hadir/Sakit/Izin)
- Face capture dengan AI

### Boss Dashboard

- Key metrics (Total employee, active, income)
- Quick approval actions
- Employee summary table
- Pending approvals

## 🐛 Troubleshooting

### Kamera tidak bekerja

- Pastikan browser memiliki permission camera
- Cek console untuk error messages
- Gunakan HTTPS (browser requirement)

### Google Maps tidak muncul

- Pastikan API Key valid
- Check API quota di Google Cloud Console
- Cek error di browser console

### Face Recognition error

- Pastikan internet connection stabil
- Cek face-api.js CDN accessibility
- Models loading tergantung network speed

## 📝 License

MIT License - Bebas digunakan untuk komersial dan non-komersial

## 👨‍💻 Support

Untuk pertanyaan atau issue, silakan buat issue di repository ini.

---

**Dibuat dengan ❤️ untuk Pabrik Beras Indonesia**
