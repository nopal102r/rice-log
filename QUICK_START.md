# 🚀 Quick Start Guide - Rice Log

## Persiapan Pertama Kali

### 1. Install Dependencies

```bash
cd rice-log
composer install
npm install
```

### 2. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Configuration

Edit `.env`:

```
DB_DATABASE=rice_log
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Create Database

```bash
# Buat database terlebih dahulu di MySQL
mysql -u root
> CREATE DATABASE rice_log;
> EXIT;

# Kemudian jalankan migration
php artisan migrate --seed
```

### 5. Storage Link

```bash
php artisan storage:link
```

### 6. Build Assets

```bash
npm run build
# atau untuk development: npm run dev
```

### 7. Jalankan Server

```bash
php artisan serve
# Akses: http://localhost:8000
```

---

## 🔑 Demo Credentials

### Login sebagai Bos (Admin):

- **Email:** `bos@ricemail.com`
- **Password:** `password`
- **Akses:** Dashboard, Employee Management, Approvals, Settings

### Login sebagai Karyawan:

- **Email:** `karyawan1@ricemail.com` sampai `karyawan10@ricemail.com`
- **Password:** `password`
- **Akses:** Dashboard, Check-in/out, Leave Request, Deposit

---

## 🗂️ Folder Structure

```
rice-log/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── EmployeeDashboardController.php
│   │   │   ├── AbsenceController.php           # GPS + Face Recognition
│   │   │   ├── LeaveSubmissionController.php
│   │   │   ├── DepositController.php
│   │   │   ├── BossDashboardController.php
│   │   │   ├── EmployeeManagementController.php
│   │   │   ├── LeaveApprovalController.php
│   │   │   ├── DepositApprovalController.php
│   │   │   ├── PayrollSettingController.php
│   │   │   └── NotificationController.php
│   │   └── Middleware/
│   │       ├── EnsureUserIsEmployee.php
│   │       └── EnsureUserIsBoss.php
│   └── Models/
│       ├── User.php
│       ├── Absence.php
│       ├── LeaveSubmission.php
│       ├── Deposit.php
│       ├── PayrollSetting.php
│       ├── MonthlySummary.php
│       └── Notification.php
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2026_02_02_000003_create_absences_table.php
│   │   ├── 2026_02_02_000004_create_leave_submissions_table.php
│   │   ├── 2026_02_02_000005_create_deposits_table.php
│   │   ├── 2026_02_02_000006_create_payroll_settings_table.php
│   │   ├── 2026_02_02_000007_create_notifications_table.php
│   │   └── 2026_02_02_000008_create_monthly_summaries_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── components/
│   │   ├── navbar.blade.php
│   │   ├── sidebar-employee.blade.php
│   │   └── sidebar-boss.blade.php
│   ├── auth/
│   │   └── login.blade.php
│   ├── employee/
│   │   ├── dashboard.blade.php
│   │   ├── absence/form.blade.php
│   │   ├── leave-submission/
│   │   │   ├── create.blade.php
│   │   │   └── my-submissions.blade.php
│   │   └── deposit/
│   │       ├── create.blade.php
│   │       └── my-deposits.blade.php
│   └── boss/
│       ├── dashboard.blade.php
│       ├── employee-management/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── show.blade.php
│       ├── leave-approval/
│       │   └── index.blade.php
│       ├── deposit-approval/
│       │   └── index.blade.php
│       └── payroll-settings/
│           └── index.blade.php
├── routes/
│   ├── web.php
│   ├── api.php
│   └── console.php
└── public/
    ├── css/app.css              # Tailwind output
    └── js/app.js                # JavaScript bundle
```

---

## 🎯 Fitur Utama

### Employee Dashboard

- Lihat ringkasan: Hadir, Sakit, Izin, Cuti (bulan ini)
- Total kg setor dan gaji yang diterima
- Tombol aksi: Presensi Masuk, Presensi Keluar, Pengajuan Cuti, Setor Beras

### Check-in/Check-out (Presensi Masuk & Keluar)

- **GPS Integration:** Google Maps dengan marker lokasi kantor
- **Distance Calculation:** Haversine formula (radius dari kantor)
- **Face Recognition:** face-api.js dengan video stream real-time
- **Status:** Hadir, Sakit, atau Izin (dengan deskripsi)
- **Warning:** Jika >2km dari kantor
- **Automatic:** Cegah double check-in/out per hari

### Leave Request (Pengajuan Cuti)

- Pilih tanggal awal dan akhir
- Auto-calculate jumlah hari
- Limit: Max 3 hari per bulan
- Status: Pending → Approved/Rejected
- Notification otomatis ke boss

### Rice Deposit (Setor Beras)

- Harus sudah check-in hari itu
- Upload foto bukti
- Input berat (kg)
- Auto-calculate harga (kg × price_per_kg)
- Status: Pending → Verified/Rejected
- Notification otomatis ke boss

### Boss Dashboard

- **KPI Cards:** Total Karyawan, Karyawan Aktif, Total Pendapatan Bulan Ini
- **Pending Actions:** List pengajuan cuti & setor yang menunggu approval
- **Employee Summary:** Tabel dengan stats setiap karyawan

### Employee Management (Manajemen Karyawan)

- **List:** Semua karyawan dengan filter (status, activity)
- **Create:** Register karyawan baru
- **Show:** Detail karyawan dengan 12-month history
- **Toggle:** Aktifkan/Nonaktifkan karyawan

### Approval Workflows

- **Leave Approval:** Approve/Reject dengan notifikasi ke employee
- **Deposit Verification:** Verify/Reject deposit dengan reason
- **Status Tracking:** Lihat riwayat lengkap

### Payroll Settings

- Harga per kg beras
- Lokasi kantor (latitude/longitude)
- Max distance allowed (warning threshold)
- Leave days per month (max)
- Min deposit per week

---

## 🔐 API Endpoints

### Notifications API

```
GET  /api/notifications              # Get unread notifications
POST /api/notifications/{id}/read    # Mark notification as read
```

### Web Routes

**Employee Routes:**

```
GET    /employee/dashboard                    # Dashboard
GET    /employee/absence/create/:type         # Absence form (masuk/keluar)
POST   /employee/absence                      # Store absence
GET    /employee/leave/create                 # Leave form
POST   /employee/leave                        # Store leave
GET    /employee/leave/my-submissions         # View my leaves
GET    /employee/deposit/create               # Deposit form
POST   /employee/deposit                      # Store deposit
GET    /employee/deposit/my-deposits          # View my deposits
GET    /api/notifications                     # Get notifications
POST   /api/notifications/{id}/read           # Mark read
```

**Boss Routes:**

```
GET    /boss/dashboard                        # Dashboard
GET    /boss/employees                        # Employee list
POST   /boss/employees/create                 # Store new employee
GET    /boss/employees/:id                    # Show employee detail
POST   /boss/employees/:id/toggle-status      # Toggle status
GET    /boss/leave-approval                   # Leave approval
POST   /boss/leave-approval/:id/approve       # Approve leave
POST   /boss/leave-approval/:id/reject        # Reject leave
GET    /boss/deposit-approval                 # Deposit approval
POST   /boss/deposit-approval/:id/verify      # Verify deposit
POST   /boss/deposit-approval/:id/reject      # Reject deposit
GET    /boss/payroll-settings                 # Settings form
POST   /boss/payroll-settings                 # Update settings
```

---

## ⚙️ Configuration Required

### Google Maps API Key

1. Go to: https://cloud.google.com/maps-platform
2. Create project
3. Enable: Maps JavaScript API
4. Create API key
5. Add to `resources/views/employee/absence/form.blade.php`:
    ```javascript
    const googleMapsApiKey = "YOUR_API_KEY_HERE";
    const map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: -6.2088, lng: 106.8456 },
        zoom: 15,
    });
    ```

### Email Configuration (Optional)

Edit `.env`:

```
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

---

## 🐛 Troubleshooting

### Camera Not Working

- Chrome/Firefox: Test in localhost (or HTTPS)
- Check browser permissions
- Verify face-api.js models loaded (check Network tab)

### Google Maps Not Showing

- Add API key to absence form view
- Check API key restrictions
- Enable Maps JavaScript API

### File Upload Fails

- Run: `php artisan storage:link`
- Check storage/app/public directory exists
- Verify file permissions (755)

### Database Errors

- Verify MySQL running
- Check .env DB credentials
- Run: `php artisan migrate:fresh --seed`

### Face Detection Slow

- face-api models cache after first load
- Check internet connection
- CPU-intensive, may lag on slow devices

---

## 📊 Database Tables

### users

- id, name, email, password, role (karyawan/bos), date_of_birth, phone, address, latitude, longitude, status (active/inactive), last_presence_at

### absences

- id, user_id, type (masuk/keluar), status (hadir/sakit/izin), description, face_image, latitude, longitude, distance_from_office, checked_at

### leave_submissions

- id, user_id, start_date, end_date, reason, status (pending/approved/rejected), approved_by, rejection_reason, approved_at

### deposits

- id, user_id, weight, price_per_kg, total_price, photo, notes, status (pending/verified/rejected), verified_by, verified_at

### payroll_settings

- id, price_per_kg, office_latitude, office_longitude, max_distance_allowed, leave_days_per_month, min_deposit_per_week

### notifications

- id, user_id, type, title, message, notifiable_type, notifiable_id, read, read_at

### monthly_summaries

- id, user_id, year, month, days_present, days_sick, days_leave, leave_approved, total_kg_deposited, total_salary, status (active/inactive)

---

## 🧪 Testing Checklist

- [ ] Login works (karyawan & bos)
- [ ] Employee dashboard shows correct stats
- [ ] Check-in: GPS tracking works
- [ ] Check-in: Face detection works
- [ ] Check-in: Distance warning appears if >2km
- [ ] Check-out: Normal (no face required)
- [ ] Leave request: Calculates days correctly
- [ ] Leave request: Prevents >3 days/month
- [ ] Leave request: Boss receives notification
- [ ] Leave approval: Employee gets notification
- [ ] Deposit: Requires check-in first
- [ ] Deposit: Photo uploads correctly
- [ ] Deposit: Price auto-calculates
- [ ] Deposit: Boss receives notification
- [ ] Deposit: Verify/reject works
- [ ] Settings: Update price works
- [ ] Employee mgmt: Create new employee
- [ ] Employee mgmt: Toggle status works
- [ ] Employee detail: 12-month history loads
- [ ] Notifications: Real-time dropdown updates
- [ ] Responsive: Mobile view (375px)
- [ ] Icons: All Font Awesome icons visible
- [ ] Colors: Gradients display correctly

---

## 📞 Support Files

- `SETUP_GUIDE.md` - Detailed setup instructions
- `IMPLEMENTATION_CHECKLIST.md` - Feature checklist
- `FINAL_STATUS.md` - Project status & achievements
- `README.md` - Main project readme

---

**Last Updated:** 2025  
**Status:** Production Ready (95% Complete)  
**Next Action:** Follow checklist above & test all features
