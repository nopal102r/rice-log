# 📋 Database Schema Documentation - Rice Log

## Overview

Sistem menggunakan 7 database tables yang saling terhubung untuk mengelola absensi, cuti, setor beras, dan perhitungan gaji karyawan pabrik.

---

## 1. users

**Purpose:** Menyimpan data user (karyawan & bos)  
**Table Name:** `users`  
**Retention:** Permanent (soft delete via status)

| Column            | Type                      | Nullable | Key    | Notes                             |
| ----------------- | ------------------------- | -------- | ------ | --------------------------------- |
| id                | bigint                    | NO       | PK     | Auto increment                    |
| name              | varchar(255)              | NO       | -      | Nama lengkap                      |
| email             | varchar(255)              | NO       | UNIQUE | Email login                       |
| email_verified_at | timestamp                 | YES      | -      | Verifikasi email                  |
| password          | varchar(255)              | NO       | -      | Hashed bcrypt                     |
| role              | enum('karyawan','bos')    | NO       | -      | Role pengguna                     |
| date_of_birth     | date                      | YES      | -      | Tanggal lahir (untuk hitung umur) |
| phone             | varchar(20)               | YES      | -      | Nomor telepon                     |
| address           | text                      | YES      | -      | Alamat rumah/kerja                |
| latitude          | decimal(10,8)             | YES      | -      | GPS latitude last check-in        |
| longitude         | decimal(10,8)             | YES      | -      | GPS longitude last check-in       |
| status            | enum('active','inactive') | NO       | INDEX  | Status karyawan                   |
| last_presence_at  | timestamp                 | YES      | -      | Last check-in time                |
| remember_token    | varchar(100)              | YES      | -      | Remember me token                 |
| created_at        | timestamp                 | NO       | -      | Created timestamp                 |
| updated_at        | timestamp                 | NO       | -      | Updated timestamp                 |

**Indexes:**

- PRIMARY KEY (id)
- UNIQUE (email)
- INDEX (role, status)

**Sample Data:**

```
role='bos': 1 boss (bos@ricemail.com)
role='karyawan': 10 employees (karyawan1-10@ricemail.com)
```

---

## 2. absences

**Purpose:** Menyimpan data presensi (check-in/check-out)  
**Table Name:** `absences`  
**Retention:** Keep forever (audit trail)  
**Related:** User

| Column               | Type                         | Nullable | Key   | Notes                         |
| -------------------- | ---------------------------- | -------- | ----- | ----------------------------- |
| id                   | bigint                       | NO       | PK    | Auto increment                |
| user_id              | bigint                       | NO       | FK    | Foreign key ke users          |
| type                 | enum('masuk','keluar')       | NO       | INDEX | Check-in atau check-out       |
| status               | enum('hadir','sakit','izin') | NO       | INDEX | Status kehadiran              |
| description          | text                         | YES      | -     | Alasan jika sakit/izin        |
| face_image           | varchar(255)                 | YES      | -     | Path ke face image di storage |
| latitude             | decimal(10,8)                | NO       | -     | GPS latitude saat check-in    |
| longitude            | decimal(10,8)                | NO       | -     | GPS longitude saat check-in   |
| distance_from_office | decimal(8,2)                 | NO       | -     | Distance dalam km             |
| checked_at           | timestamp                    | NO       | -     | Waktu check-in/out            |
| created_at           | timestamp                    | NO       | -     | Created timestamp             |
| updated_at           | timestamp                    | NO       | -     | Updated timestamp             |

**Indexes:**

- PRIMARY KEY (id)
- FOREIGN KEY (user_id) references users(id) on delete cascade
- INDEX (user_id, type, checked_at)
- INDEX (status)

**Business Rules:**

- Hanya 1 'masuk' per hari per user
- Hanya 1 'keluar' per hari per user (setelah 'masuk')
- Status 'hadir' = normal presence
- Status 'sakit' = sick leave (tidak perlu face, cuma deskripsi)
- Status 'izin' = permission (tidak perlu face, cuma deskripsi)
- Face image hanya untuk 'hadir' di type 'masuk'
- Distance > 2km = warning sign (tidak prevent, hanya alert)

**Query Examples:**

```sql
-- Today's check-ins for user
SELECT * FROM absences
WHERE user_id = 1 AND DATE(checked_at) = CURDATE();

-- Monthly summary
SELECT COUNT(*) as total, status, COUNT(DISTINCT DATE(checked_at)) as days
FROM absences
WHERE user_id = 1 AND MONTH(checked_at) = MONTH(NOW())
GROUP BY status;

-- Distance violations (>2km)
SELECT * FROM absences
WHERE distance_from_office > 2.0
ORDER BY checked_at DESC;
```

---

## 3. leave_submissions

**Purpose:** Menyimpan data pengajuan cuti/izin  
**Table Name:** `leave_submissions`  
**Retention:** Keep forever  
**Related:** User (requester), User (approver)

| Column           | Type                                  | Nullable | Key   | Notes                                       |
| ---------------- | ------------------------------------- | -------- | ----- | ------------------------------------------- |
| id               | bigint                                | NO       | PK    | Auto increment                              |
| user_id          | bigint                                | NO       | FK    | Karyawan yang request                       |
| start_date       | date                                  | NO       | -     | Tanggal awal cuti                           |
| end_date         | date                                  | NO       | -     | Tanggal akhir cuti                          |
| reason           | text                                  | YES      | -     | Alasan cuti (optional)                      |
| status           | enum('pending','approved','rejected') | NO       | INDEX | Status approval                             |
| approved_by      | bigint                                | YES      | FK    | Bos yang approve (null if pending/rejected) |
| rejection_reason | text                                  | YES      | -     | Alasan reject (jika status=rejected)        |
| approved_at      | timestamp                             | YES      | -     | Waktu approval                              |
| created_at       | timestamp                             | NO       | -     | Created timestamp                           |
| updated_at       | timestamp                             | NO       | -     | Updated timestamp                           |

**Indexes:**

- PRIMARY KEY (id)
- FOREIGN KEY (user_id) references users(id) on delete cascade
- FOREIGN KEY (approved_by) references users(id) on delete set null
- INDEX (user_id, status)
- INDEX (status, approved_at)

**Business Rules:**

- Max 3 hari/bulan per karyawan (enforced in controller)
- Tanggal awal <= tanggal akhir (validated in controller)
- Total hari = DATE_DIFF(end_date, start_date) + 1
- Hanya approved leaves di-count untuk monthly summary
- Notification otomatis dikirim ke semua bos saat submit

**Validations:**

```php
// Total days calculation
$totalDays = $endDate->diffInDays($startDate) + 1;

// Check monthly limit
$approvedDaysThisMonth = LeaveSubmission::where('user_id', $userId)
    ->where('status', 'approved')
    ->whereMonth('start_date', now()->month)
    ->get()
    ->sum(fn($leave) => $leave->getTotalDays());

if ($approvedDaysThisMonth + $totalDays > 3) {
    // Error: Exceeds limit
}
```

**Query Examples:**

```sql
-- Pending leaves for boss
SELECT * FROM leave_submissions
WHERE status = 'pending'
ORDER BY created_at DESC;

-- Approved leaves for employee this month
SELECT * FROM leave_submissions
WHERE user_id = 1 AND status = 'approved'
AND MONTH(start_date) = MONTH(NOW());

-- Total approved days this month
SELECT SUM(DATEDIFF(end_date, start_date) + 1) as total_days
FROM leave_submissions
WHERE user_id = 1 AND status = 'approved'
AND MONTH(start_date) = MONTH(NOW());
```

---

## 4. deposits

**Purpose:** Menyimpan data setor beras  
**Table Name:** `deposits`  
**Retention:** Keep forever  
**Related:** User (depositor), User (verifier)

| Column       | Type                                  | Nullable | Key   | Notes                       |
| ------------ | ------------------------------------- | -------- | ----- | --------------------------- |
| id           | bigint                                | NO       | PK    | Auto increment              |
| user_id      | bigint                                | NO       | FK    | Karyawan yang setor         |
| weight       | decimal(8,2)                          | NO       | -     | Berat beras dalam kg        |
| price_per_kg | decimal(10,2)                         | NO       | -     | Harga per kg (snapshot)     |
| total_price  | decimal(12,2)                         | NO       | -     | weight × price_per_kg       |
| photo        | varchar(255)                          | NO       | -     | Path ke foto bukti          |
| notes        | text                                  | YES      | -     | Catatan tambahan (optional) |
| status       | enum('pending','verified','rejected') | NO       | INDEX | Status verifikasi           |
| verified_by  | bigint                                | YES      | FK    | Bos yang verify             |
| verified_at  | timestamp                             | YES      | -     | Waktu verifikasi            |
| created_at   | timestamp                             | NO       | -     | Created timestamp           |
| updated_at   | timestamp                             | NO       | -     | Updated timestamp           |

**Indexes:**

- PRIMARY KEY (id)
- FOREIGN KEY (user_id) references users(id) on delete cascade
- FOREIGN KEY (verified_by) references users(id) on delete set null
- INDEX (user_id, status)
- INDEX (status, verified_at)

**Business Rules:**

- Karyawan HARUS sudah check-in hari itu untuk setor (enforced in controller)
- Weight minimum 0.1 kg
- Photo wajib (bukti beras)
- Harga snapshot untuk audit (jangan berubah meski setting berubah)
- Hanya verified deposits di-count untuk salary calculation
- Min 1 setor per minggu (enforcement di controller belum ada, optional)

**Validations:**

```php
// Check if checked in today
if (!Absence::hasCheckedInToday($userId)) {
    return error("Must check-in first");
}

// Get current price from settings
$settings = PayrollSetting::getCurrent();
$totalPrice = $weight * $settings->price_per_kg;
```

**Query Examples:**

```sql
-- Pending deposits for verification
SELECT d.*, u.name FROM deposits d
JOIN users u ON d.user_id = u.id
WHERE d.status = 'pending'
ORDER BY d.created_at DESC;

-- Total verified deposits this month
SELECT SUM(weight) as total_kg, SUM(total_price) as total_price
FROM deposits
WHERE user_id = 1 AND status = 'verified'
AND MONTH(created_at) = MONTH(NOW());

-- Last 7 days deposits
SELECT * FROM deposits
WHERE user_id = 1 AND status = 'verified'
AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY);
```

---

## 5. payroll_settings

**Purpose:** Menyimpan konfigurasi sistem  
**Table Name:** `payroll_settings`  
**Retention:** Only 1 record (singleton)  
**Related:** -

| Column               | Type          | Nullable | Key | Notes                                         |
| -------------------- | ------------- | -------- | --- | --------------------------------------------- |
| id                   | bigint        | NO       | PK  | Always 1                                      |
| price_per_kg         | decimal(10,2) | NO       | -   | Harga beras per kg (Rp)                       |
| office_latitude      | decimal(10,8) | NO       | -   | Kantor latitude (reference point)             |
| office_longitude     | decimal(10,8) | NO       | -   | Kantor longitude (reference point)            |
| max_distance_allowed | decimal(8,2)  | NO       | -   | Max distance for warning (km)                 |
| leave_days_per_month | int           | NO       | -   | Max leave days per month (default 3)          |
| min_deposit_per_week | int           | NO       | -   | Min deposit requirements per week (default 1) |
| created_at           | timestamp     | NO       | -   | Created timestamp                             |
| updated_at           | timestamp     | NO       | -   | Updated timestamp                             |

**Default Values:**

```php
$settings = [
    'price_per_kg' => 15000, // Rp per kg
    'office_latitude' => -6.2088,
    'office_longitude' => 106.8456, // Jakarta koordinat
    'max_distance_allowed' => 2.0, // km
    'leave_days_per_month' => 3,
    'min_deposit_per_week' => 1,
];
```

**Access Pattern:**

```php
// Singleton pattern - always use this
$settings = PayrollSetting::getCurrent();
// Use: $settings->price_per_kg, $settings->office_latitude, etc
```

**Admin Update:**

```php
// Boss dapat update via /boss/payroll-settings form
PayrollSetting::first()->update([
    'price_per_kg' => 16000,
    'office_latitude' => -6.2100,
    'office_longitude' => 106.8500,
    'max_distance_allowed' => 2.5,
    'leave_days_per_month' => 3,
    'min_deposit_per_week' => 1,
]);
```

---

## 6. notifications

**Purpose:** Menyimpan notifikasi real-time untuk users  
**Table Name:** `notifications`  
**Retention:** Keep 30 days (optional cleanup job)  
**Related:** User (recipient), Polymorphic (deposit/leave/etc)

| Column          | Type         | Nullable | Key   | Notes                               |
| --------------- | ------------ | -------- | ----- | ----------------------------------- |
| id              | bigint       | NO       | PK    | UUID format                         |
| user_id         | bigint       | NO       | FK    | Recipient user                      |
| type            | varchar(255) | NO       | -     | Notification type (string)          |
| title           | varchar(255) | NO       | -     | Short title                         |
| message         | text         | NO       | -     | Full message                        |
| notifiable_type | varchar(255) | YES      | -     | Related model (Leave, Deposit, etc) |
| notifiable_id   | bigint       | YES      | -     | Related model ID                    |
| read            | boolean      | NO       | INDEX | Read status (default false)         |
| read_at         | timestamp    | YES      | -     | Waktu di-read                       |
| created_at      | timestamp    | NO       | -     | Created timestamp                   |
| updated_at      | timestamp    | NO       | -     | Updated timestamp                   |

**Indexes:**

- PRIMARY KEY (id)
- FOREIGN KEY (user_id) references users(id) on delete cascade
- INDEX (user_id, read, created_at)

**Notification Types:**

```
- 'leave_pending'       → Employee requested leave (notify boss)
- 'leave_approved'      → Leave was approved (notify employee)
- 'leave_rejected'      → Leave was rejected (notify employee)
- 'deposit_pending'     → Employee deposited rice (notify boss)
- 'deposit_verified'    → Deposit was verified (notify employee)
- 'deposit_rejected'    → Deposit was rejected (notify employee)
```

**Polymorphic Usage:**

```php
// Create notification linked to leave
Notification::create([
    'user_id' => $boss->id,
    'type' => 'leave_pending',
    'title' => 'Pengajuan Cuti Baru',
    'message' => $employee->name . ' mengajukan cuti',
    'notifiable_type' => LeaveSubmission::class, // "App\Models\LeaveSubmission"
    'notifiable_id' => $leave->id,
]);

// Retrieve and access related leave
$notification->notifiable; // Returns LeaveSubmission instance
```

**Query Examples:**

```sql
-- Unread notifications for user
SELECT * FROM notifications
WHERE user_id = 1 AND read = false
ORDER BY created_at DESC;

-- Mark as read
UPDATE notifications SET read = true, read_at = NOW()
WHERE id = 123 AND user_id = 1;

-- Cleanup old read notifications (30 days)
DELETE FROM notifications
WHERE read = true
AND read_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
```

---

## 7. monthly_summaries

**Purpose:** Menyimpan ringkasan bulanan untuk gaji & reporting  
**Table Name:** `monthly_summaries`  
**Retention:** Keep forever  
**Related:** User

| Column             | Type                      | Nullable | Key | Notes                              |
| ------------------ | ------------------------- | -------- | --- | ---------------------------------- |
| id                 | bigint                    | NO       | PK  | Auto increment                     |
| user_id            | bigint                    | NO       | FK  | Karyawan                           |
| year               | int                       | NO       | -   | Tahun (e.g., 2025)                 |
| month              | int                       | NO       | -   | Bulan (1-12)                       |
| days_present       | int                       | NO       | -   | Jumlah hari hadir                  |
| days_sick          | int                       | NO       | -   | Jumlah hari sakit                  |
| days_leave         | int                       | NO       | -   | Jumlah hari izin (tidak disetujui) |
| leave_approved     | int                       | NO       | -   | Jumlah hari cuti approved          |
| total_kg_deposited | decimal(10,2)             | NO       | -   | Total kg beras setor (verified)    |
| total_salary       | decimal(12,2)             | NO       | -   | Total gaji (kg × price)            |
| status             | enum('active','inactive') | NO       | -   | Active = hadir > 0                 |
| created_at         | timestamp                 | NO       | -   | Created timestamp                  |
| updated_at         | timestamp                 | NO       | -   | Updated timestamp                  |

**Constraints:**

- UNIQUE (user_id, year, month) - Hanya 1 record per karyawan per bulan
- Index pada (user_id, year, month) untuk query cepat

**Indexes:**

- PRIMARY KEY (id)
- UNIQUE KEY (user_id, year, month)
- FOREIGN KEY (user_id) references users(id) on delete cascade
- INDEX (status, created_at)

**Auto-Calculation Logic:**

```php
public function calculateSummary()
{
    $year = $this->year;
    $month = $this->month;

    // Count absences by status
    $this->days_present = Absence::where('user_id', $this->user_id)
        ->where('status', 'hadir')
        ->whereYear('checked_at', $year)
        ->whereMonth('checked_at', $month)
        ->distinct('DATE(checked_at)')
        ->count();

    $this->days_sick = Absence::where('user_id', $this->user_id)
        ->where('status', 'sakit')
        ->whereYear('checked_at', $year)
        ->whereMonth('checked_at', $month)
        ->distinct('DATE(checked_at)')
        ->count();

    // Approved leaves only
    $this->leave_approved = LeaveSubmission::where('user_id', $this->user_id)
        ->where('status', 'approved')
        ->whereYear('start_date', $year)
        ->whereMonth('start_date', $month)
        ->get()
        ->sum(fn($leave) => $leave->getTotalDays());

    // Verified deposits only
    $deposits = Deposit::getTotalMonthDeposits($this->user_id, $month, $year);
    $this->total_kg_deposited = $deposits['total_kg'];
    $this->total_salary = $deposits['total_price'];

    // Set status: active if hadir > 0
    $this->status = $this->days_present > 0 ? 'active' : 'inactive';

    $this->save();
}
```

**Query Examples:**

```sql
-- Current month summary for employee
SELECT * FROM monthly_summaries
WHERE user_id = 1
AND year = YEAR(NOW())
AND month = MONTH(NOW());

-- 12-month history
SELECT * FROM monthly_summaries
WHERE user_id = 1
AND year = 2025
ORDER BY month;

-- Boss dashboard: Total monthly income
SELECT SUM(total_salary) as total_income
FROM monthly_summaries
WHERE year = YEAR(NOW())
AND month = MONTH(NOW());

-- Employee activity (active months)
SELECT COUNT(*) as active_months
FROM monthly_summaries
WHERE user_id = 1 AND status = 'active' AND year = 2025;
```

---

## Relationship Diagram

```
users (1)
├── (1) ──→ (M) absences
├── (1) ──→ (M) leave_submissions
├── (1) ──→ (M) deposits
├── (1) ──→ (M) monthly_summaries
└── (1) ──→ (M) notifications

leave_submissions (M)
├── (M) ──→ (1) users (requester)
└── (M) ──→ (1) users (approver as approved_by)

deposits (M)
├── (M) ──→ (1) users (depositor)
└── (M) ──→ (1) users (verifier as verified_by)

notifications (M)
├── (M) ──→ (1) users (recipient)
└── (M) ──→ polymorphic (leave_submissions, deposits, etc)
```

---

## Data Integrity Constraints

### Foreign Key Actions:

- `ON DELETE CASCADE` untuk absences, leave_submissions, deposits, monthly_summaries
    - Jika user dihapus, semua datanya ikut terhapus
- `ON DELETE SET NULL` untuk notifications (approved_by, verified_by)
    - Jika approver/verifier dihapus, field jadi NULL (data tetap ada)

### Unique Constraints:

- users: email (UNIQUE)
- monthly_summaries: (user_id, year, month) composite unique

### Check Constraints (enforced in application):

- absences: distance_from_office >= 0
- deposits: weight > 0, price_per_kg > 0, total_price > 0
- leave_submissions: start_date <= end_date
- payroll_settings: price_per_kg > 0, max_distance_allowed > 0

---

## Typical Data Flow

### Check-in Process:

```
user → AbsenceController.store()
    → Absence.create() [type='masuk', status set by user]
    → MonthlySummary.getOrCreateCurrent()
    → MonthlySummary.calculateSummary() [updates days_present if hadir]
    → Notification.create() [if sakit, notify boss]
```

### Leave Request Process:

```
user → LeaveSubmissionController.store()
    → validate: max 3 days/month
    → LeaveSubmission.create() [status='pending']
    → Notification.create() [notify all bosses]
    → boss reviews
    → LeaveApprovalController.approve/reject()
    → LeaveSubmission.update() [status='approved'/'rejected']
    → Notification.create() [notify employee]
    → MonthlySummary.calculateSummary() [updates leave_approved if approved]
```

### Rice Deposit Process:

```
user → DepositController.store()
    → verify: user checked in today
    → Deposit.create() [status='pending']
    → Notification.create() [notify all bosses]
    → boss reviews photo
    → DepositApprovalController.verify/reject()
    → Deposit.update() [status='verified'/'rejected']
    → Notification.create() [notify employee]
    → MonthlySummary.calculateSummary() [updates total_kg_deposited, total_salary if verified]
```

### Salary Calculation:

```
total_salary = total_kg_deposited × price_per_kg
where:
    - total_kg_deposited = SUM(deposits.weight) WHERE status='verified' AND month=current
    - price_per_kg = from payroll_settings (snapshot at deposit time)
```

---

## Performance Considerations

### Indexes for Common Queries:

- `absences(user_id, type, checked_at)` → Check duplicate check-in/out
- `absences(user_id, status, checked_at)` → Count by status
- `leave_submissions(user_id, status, start_date)` → Monthly limit check
- `deposits(user_id, status, created_at)` → Verified deposits sum
- `monthly_summaries(user_id, year, month)` → Fast dashboard lookup
- `notifications(user_id, read, created_at)` → Unread notifications

### Query Optimization:

```php
// ❌ BAD - N+1 queries
foreach ($users as $user) {
    $summary = MonthlySummary::where('user_id', $user->id)->first();
}

// ✅ GOOD - Single query
$summaries = MonthlySummary::with('user')->get();

// ✅ GOOD - Eager loading
$users = User::with('monthlySummaries')->get();
```

---

## Migration Commands

```bash
# Create tables
php artisan migrate

# Fresh (drop all + recreate)
php artisan migrate:fresh

# Fresh + seed
php artisan migrate:fresh --seed

# Rollback
php artisan migrate:rollback

# Rollback + remigrate
php artisan migrate:refresh
```

---

**Last Updated:** 2025  
**Version:** 1.0  
**Status:** Production Ready
