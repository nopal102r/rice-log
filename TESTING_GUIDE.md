# 🧪 Comprehensive Testing Guide - Rice Log

## Pre-Testing Checklist

- [ ] Environment setup complete (.env configured)
- [ ] Database migrated with seeder (`php artisan migrate --seed`)
- [ ] Storage link created (`php artisan storage:link`)
- [ ] Assets built (`npm run build`)
- [ ] Server running (`php artisan serve`)
- [ ] Browser open at `http://localhost:8000`
- [ ] Google Maps API key added (if testing GPS)

---

## 1️⃣ Authentication Testing

### Login as Boss

**Test Case:** Boss Login Success

```
1. Go to http://localhost:8000
2. Email: bos@ricemail.com
3. Password: password
4. Click "Login"

Expected:
- ✅ Redirects to /boss/dashboard
- ✅ Can see "Dashboard Bos" header
- ✅ Navigation shows boss menu items
- ✅ Sidebar shows boss options (Employee Mgmt, Leave Approval, etc)
```

### Login as Employee

**Test Case:** Employee Login Success

```
1. Go to http://localhost:8000
2. Email: karyawan1@ricemail.com
3. Password: password
4. Click "Login"

Expected:
- ✅ Redirects to /employee/dashboard
- ✅ Can see "Dashboard Karyawan" or stats cards
- ✅ Navigation shows employee menu items
- ✅ Sidebar shows employee options (Presensi, Pengajuan, etc)
```

### Failed Login

**Test Case:** Invalid Credentials

```
1. Enter invalid email/password
2. Click "Login"

Expected:
- ✅ Shows error message: "These credentials do not match our records."
- ✅ Stays on login page
- ✅ Form fields are cleared
```

### Unauthorized Access

**Test Case:** Employee accessing Boss Routes

```
1. Login as karyawan1@ricemail.com
2. Manually go to: http://localhost:8000/boss/employees
3. (or boss accessing employee routes)

Expected:
- ✅ Redirected to /employee/dashboard or /boss/dashboard (unauthorized)
- ✅ No error shown (soft redirect for UX)
```

---

## 2️⃣ Employee Dashboard Testing

### View Dashboard Stats

**Test Case:** Dashboard displays correct month stats

```
1. Login as karyawan1@ricemail.com
2. View dashboard

Check visible:
- [ ] Stat card "Hadir" (should be some number)
- [ ] Stat card "Sakit" (should be 0 or more)
- [ ] Stat card "Izin" (should be 0 or more)
- [ ] Stat card "Cuti" (should be 0 or more)
- [ ] "Total kg Setor" and "Total Harga"
- [ ] 4 action buttons: Presensi Masuk, Presensi Keluar, Pengajuan Cuti, Setor Beras

Expected:
- ✅ All cards render without errors
- ✅ Numbers are non-negative
- ✅ Button styling is consistent
```

### Recent Activities

**Test Case:** Dashboard shows recent activity

```
1. Stay on dashboard
2. Scroll down to "Recent Activities" section

Check visible:
- [ ] "Recent Absences" table (should be empty or have entries)
- [ ] "Recent Deposits" table (should be empty or have entries)

Expected:
- ✅ Tables render
- ✅ If no data, shows "No data" message or empty table
- ✅ If has data, shows correct info (date, type, status)
```

### Notification Dropdown

**Test Case:** Notification dropdown works

```
1. On dashboard, look at top navbar
2. Find bell icon
3. Click bell icon

Expected:
- ✅ Dropdown appears
- ✅ Shows "Notifications" with count badge (if any)
- ✅ Can see notification items or "No notifications"
- ✅ Click notification closes dropdown
```

---

## 3️⃣ Check-in/Check-out Testing

### Access Presensi Masuk Form

**Test Case:** Open check-in form

```
1. On employee dashboard
2. Click "Presensi Masuk" button

Expected:
- ✅ Redirects to check-in form
- ✅ Page title shows "Presensi Masuk"
- ✅ Google Maps loads (showing office marker)
- ✅ Form visible with fields:
  - [ ] Google Maps container
  - [ ] "Your Location" info (or loading)
  - [ ] "Jarak dari kantor" display
  - [ ] Status radio buttons: Hadir, Sakit, Izin
  - [ ] Description textarea (optional, enabled for Sakit/Izin)
  - [ ] Video stream box (for face recognition)
  - [ ] "Capture Face" button (if status=Hadir)
  - [ ] Submit button
```

### GPS Functionality

**Test Case:** GPS tracking works

```
1. On presensi masuk form
2. Allow browser location access (prompt should appear)
3. Wait 2-3 seconds

Expected:
- ✅ "Your Location" section updates with latitude/longitude
- ✅ User marker appears on map (blue)
- ✅ Distance line drawn from office to user location
- ✅ "Jarak dari kantor" shows distance in km
- ✅ If distance > 2km, warning box appears (red with ⚠️ icon)

**Note:** Test with different locations:
- Near office: distance < 1km (no warning)
- 5km away: distance > 2km (warning appears)
```

### Face Recognition - Hadir Status

**Test Case:** Face detection works

```
1. On presensi masuk form
2. Select "Hadir" radio button
3. Wait for video stream to load

Expected:
- ✅ Video stream appears in designated box
- ✅ "Capture Face" button becomes enabled
- ✅ Face detection runs every 100ms
- ✅ When face detected, draws canvas overlay (optional visual)
- ✅ "Click to Capture" button ready

4. Position face in frame
5. Click "Capture Face" button

Expected:
- ✅ Photo captured from video stream
- ✅ Image preview shows in form
- ✅ Canvas overlay disappears
```

### Face Recognition - Sakit/Izin Status

**Test Case:** Face disabled for non-hadir status

```
1. On presensi masuk form
2. Select "Sakit" radio button

Expected:
- ✅ Video stream section disappears
- ✅ Description textarea becomes REQUIRED and ENABLED
- ✅ "Capture Face" button hidden

3. Select "Izin" radio button

Expected:
- ✅ Video stream still hidden
- ✅ Description textarea still REQUIRED
```

### Distance Warning

**Test Case:** Distance warning appears if >2km

```
1. On presensi masuk form
2. Simulate location far from office (mock GPS if needed)
3. Distance should show > 2km

Expected:
- ✅ Red warning box appears below map with text:
  "⚠️ Anda terlalu jauh dari kantor utama (X km). Pastikan ada alasan yang valid."
- ✅ Can still submit form (warning only, not blocking)
```

### Submit Check-in

**Test Case:** Submit presensi masuk

```
1. Fill form:
   - [ ] Select status (Hadir)
   - [ ] Capture face (if Hadir)
   - [ ] OR add description (if Sakit/Izin)
2. Click "Submit" button

Expected:
- ✅ Form submits via AJAX
- ✅ Loading indicator shows
- ✅ On success: SweetAlert confirmation appears
- ✅ Alert message: "Presensi masuk berhasil dicatat"
- ✅ Redirects to dashboard after 2 seconds
- ✅ Dashboard updates with new data

**Error Cases:**
- Already checked in today → Error: "Anda sudah melakukan presensi masuk hari ini"
- No face captured (for Hadir) → Error: "Silakan capture wajah terlebih dahulu"
- Description empty (for Sakit/Izin) → Error: "Alasan tidak boleh kosong"
```

### Access Presensi Keluar Form

**Test Case:** Check-out form

```
1. On employee dashboard
2. Click "Presensi Keluar" button

Expected:
- ✅ Redirects to check-out form
- ✅ Same layout as check-in BUT:
  - [ ] No face recognition section (always disabled)
  - [ ] No status selection (always "Keluar")
  - [ ] Description optional
  - [ ] GPS tracking still works
```

### Submit Check-out

**Test Case:** Submit presensi keluar

```
1. Fill check-out form:
   - [ ] GPS auto-loads
   - [ ] Optional description
2. Click "Submit"

Expected:
- ✅ Same AJAX submission
- ✅ Success: "Presensi keluar berhasil dicatat"
- ✅ Cannot check-out twice same day → Error: "Anda sudah melakukan presensi keluar hari ini"
- ✅ Cannot check-out before check-in → Error: "Silakan lakukan presensi masuk terlebih dahulu"
```

---

## 4️⃣ Leave Request Testing

### Access Leave Request Form

**Test Case:** Open pengajuan cuti

```
1. On employee dashboard
2. Click "Pengajuan Cuti" button

Expected:
- ✅ Redirects to leave form
- ✅ Page shows:
  - [ ] "Pengajuan Cuti" header
  - [ ] Date range inputs (start_date, end_date)
  - [ ] Reason textarea
  - [ ] Dynamic "Jumlah Hari" display
  - [ ] Submit button
  - [ ] Info box showing max 3 hari/bulan
```

### Date Selection

**Test Case:** Select leave dates

```
1. On leave form
2. Click "Tanggal Mulai" field
3. Select date (e.g., 2025-02-10)
4. Click "Tanggal Akhir" field
5. Select date (e.g., 2025-02-12)

Expected:
- ✅ Dates populate in fields
- ✅ "Jumlah Hari" auto-updates to: 3 (Feb 10, 11, 12)
- ✅ Calculation: (end_date - start_date) + 1

Test edge cases:
- Same day: select Feb 10 → Feb 10 = 1 hari ✅
- 1 day apart: select Feb 10 → Feb 11 = 2 hari ✅
```

### Monthly Limit Enforcement

**Test Case:** Cannot exceed 3 days/month

```
1. On leave form
2. Try to submit: Feb 10-15 (6 days)
3. Click "Submit"

Expected:
- ✅ Form validates client-side (optional)
- ✅ Server validation rejects with error:
  "Pengajuan cuti melebihi limit 3 hari per bulan. Anda sudah mengajukan X hari."
- ✅ Form stays open, user can adjust dates

4. Submit: Feb 10-12 (3 days, within limit)

Expected:
- ✅ Form submits successfully
- ✅ SweetAlert: "Pengajuan cuti berhasil dikirim untuk persetujuan"
- ✅ Notification created for bosses
```

### View Leave History

**Test Case:** View submitted leaves

```
1. On employee dashboard (or in sidebar menu)
2. Click "Riwayat Pengajuan Cuti" or similar link
3. Go to: /employee/leave/my-submissions

Expected:
- ✅ Page shows list of all leave submissions
- [ ] Table columns: Tanggal, Hari, Reason, Status, Actions
- [ ] Status badges: "pending" (yellow), "approved" (green), "rejected" (red)
- [ ] Sort by latest first
- [ ] If no submissions: "No data" message

**Test Status Updates:**
If leave was approved by boss:
- ✅ Status shows "Approved"
- ✅ Shows approval date/time
- ✅ Shows approver name (optional)
```

---

## 5️⃣ Rice Deposit Testing

### Access Deposit Form

**Test Case:** Open setor beras form

```
1. On employee dashboard
2. Click "Setor Beras" button

Expected:
- ✅ Redirects to deposit form
- ✅ Page shows:
  - [ ] Form title: "Setor Beras"
  - [ ] Weight input field (numeric, min 0.1)
  - [ ] Photo upload field
  - [ ] Notes textarea (optional)
  - [ ] "Harga per kg" display (from settings)
  - [ ] "Total Harga" display (auto-calculated)
  - [ ] Submit button
```

### Validation - Must Check-in First

**Test Case:** Cannot deposit without check-in

```
1. On deposit form
2. Before checking in, try to submit deposit

Expected:
- ✅ Form submission fails with error:
  "Anda harus melakukan absen masuk terlebih dahulu sebelum dapat melakukan setor."
- ✅ Form stays open
- ✅ User redirected to dashboard or check-in form
```

### Weight Input

**Test Case:** Enter rice weight

```
1. After check-in, go to deposit form
2. In "Berat (kg)" field, enter: 50

Expected:
- ✅ Field accepts numeric input
- ✅ "Total Harga" auto-calculates: 50 × price_per_kg = result
- ✅ Try invalid: letters, negative numbers → validation error

Test with different amounts:
- 0.1 kg → Minimum allowed ✅
- 100 kg → Large amount ✅
- 0 kg → Error: "must be at least 0.1" ✅
- -10 kg → Error ✅
```

### Photo Upload

**Test Case:** Upload deposit photo

```
1. Click "Choose Photo" button
2. Select image file (JPG, PNG, max 5MB)

Expected:
- ✅ File selected shown in form
- ✅ File size warning if > 5MB
- ✅ Format validation (jpeg/png only)

Test invalid files:
- PDF file → Error: "must be image" ✅
- 10MB image → Error: "max 5120 KB" ✅
- GIF file → Accepted if format allows ✅
```

### Submit Deposit

**Test Case:** Submit rice deposit

```
1. Fill form:
   - [ ] Weight: 25 kg
   - [ ] Upload photo
   - [ ] Optional: Notes
2. Click "Submit"

Expected:
- ✅ Form submits via AJAX
- ✅ Photo uploaded to storage/deposits/YYYY/MM/DD/
- ✅ SweetAlert success: "Setor berhasil dicatat. Tunggu verifikasi dari atasan"
- ✅ Notification created for boss
- ✅ Redirect to dashboard
```

### View Deposit History

**Test Case:** View submitted deposits

```
1. On employee dashboard (or sidebar)
2. Click "Riwayat Setor" or similar
3. Go to: /employee/deposit/my-deposits

Expected:
- ✅ Page shows list of deposits
- [ ] Table columns: Tanggal, Kg, Harga/kg, Total, Status, Actions
- [ ] Status badges: "pending" (yellow), "verified" (green), "rejected" (red)
- [ ] Photo preview/thumbnail (hover to see full image)
- [ ] Pagination if > 15 items
- [ ] Total summary: "Total bulan ini: X kg, Rp Y"
```

---

## 6️⃣ Boss Dashboard Testing

### View Boss Dashboard

**Test Case:** Boss dashboard displays KPIs

```
1. Login as bos@ricemail.com
2. Go to /boss/dashboard

Expected:
- ✅ Page title: "Dashboard Bos" or "Admin Dashboard"
- ✅ 3 KPI cards visible:
  - [ ] "Total Karyawan": 10
  - [ ] "Karyawan Aktif": X (based on seeded data)
  - [ ] "Pendapatan Bulan Ini": Rp XXXX

4 Action buttons:
  - [ ] "Tambah Karyawan" (blue gradient)
  - [ ] "Persetujuan Cuti" (green gradient)
  - [ ] "Verifikasi Setor" (orange gradient)
  - [ ] "Pengaturan" (purple gradient)
```

### Pending Approvals Display

**Test Case:** View pending leaves and deposits

```
1. On boss dashboard
2. Scroll down to "Pending Actions" sections

Pending Leaves Section:
- [ ] List shows pending leave submissions
- [ ] Card shows: Employee name, date range, total days, status badge
- [ ] "Approve" and "Reject" buttons inline
- [ ] Count badge: "Pending: X"

Pending Deposits Section:
- [ ] List shows pending deposits
- [ ] Card shows: Employee name, kg, price, total, status badge
- [ ] "Verify" and "Reject" buttons inline

Expected:
- ✅ Both sections render without errors
- ✅ If no pending items, shows "No pending" message
- ✅ If items exist, all data displays correctly
```

### Employee Summary Table

**Test Case:** View all employees summary

```
1. On boss dashboard
2. Scroll to "Employee Summary" table

Expected:
- ✅ Table shows all employees
- [ ] Columns: Name, Email, Age, Status, Activity, Hadir, Kg Setor, Gaji, Actions
- [ ] 10 karyawan listed
- [ ] Each has calculated stats for current month
- [ ] Age calculated from date_of_birth
- [ ] Can toggle status (active/inactive)
- [ ] Can view detail
```

---

## 7️⃣ Employee Management Testing

### View Employee List

**Test Case:** Boss views all employees

```
1. Login as boss
2. Click "Tambah Karyawan" OR "Manajemen Karyawan" in sidebar
3. Should land on employee list page

Expected:
- ✅ Page title: "Daftar Karyawan" or "Employee Management"
- ✅ Table with columns: Name, Email, Age, Status, Activity, Hadir, Kg, Gaji, Actions
- [ ] "Tambah Karyawan Baru" button
- [ ] Filter form: Status dropdown, Activity dropdown, Search box
- [ ] Pagination if > 15 items
```

### Filter Employees

**Test Case:** Filter by status

```
1. Click Status filter dropdown
2. Select "Active"
3. Click "Filter" or auto-applies

Expected:
- ✅ Table reloads showing only active employees
- ✅ "Inactive" employees hidden
- ✅ Pagination updates

Repeat with:
- "Inactive" filter
- "All" filter
```

### Create New Employee

**Test Case:** Register new employee

```
1. Click "Tambah Karyawan Baru" button
2. Redirects to create form

Fill form:
- [ ] Name: "Karyawan Baru"
- [ ] Email: "karyawan_baru@ricemail.com"
- [ ] Phone: "081234567890"
- [ ] Date of Birth: "1995-05-15"
- [ ] Password: "password123"
- [ ] Confirm Password: "password123"
- [ ] Address: "Jl. Utama No. 123"

3. Click "Simpan"

Expected:
- ✅ Form validates:
  - Email unique check
  - Password min 8 chars
  - Password confirmation match
- ✅ SweetAlert confirmation: "Simpan karyawan baru?"
- ✅ On confirmation, creates employee
- ✅ Redirects to employee list
- ✅ New employee visible in list with status "active"

Test error cases:
- Duplicate email → Error: "email sudah terdaftar"
- Password too short → Error: "min 8 characters"
- Passwords don't match → Error: "confirmation mismatch"
```

### View Employee Detail

**Test Case:** Boss views individual employee

```
1. On employee list
2. Click employee name or "View" button
3. Go to: /boss/employees/1

Expected:
- ✅ Page shows employee detail
- [ ] Employee name as header
- [ ] 4 KPI cards: Status, Hadir, Kg Setor, Gaji
- [ ] Personal info box: name, email, phone, DOB, address
- [ ] Monthly stats grid (2025): hadir, sakit, izin, cuti, kg, gaji
- [ ] Recent absences table (last 10)
  - [ ] Shows distance with ⚠️ if >2km
- [ ] Recent deposits table (last 10)
  - [ ] Shows status badge

Scroll to see more:
- [ ] 12-month history (if available)
- [ ] Charts or stats (optional)
```

### Toggle Employee Status

**Test Case:** Activate/Deactivate employee

```
1. On employee list
2. Find employee row
3. Click toggle button (icon in Status column)

Expected:
- ✅ Status toggles: active → inactive or vice versa
- ✅ Visual feedback (loading spinner)
- ✅ Row updates after toggle
- ✅ Filter adjusts if toggled outside current filter
```

---

## 8️⃣ Leave Approval Testing

### Access Leave Approval

**Test Case:** Boss reviews leaves

```
1. Login as boss
2. Click "Persetujuan Cuti" or navigate to /boss/leave-approval

Expected:
- ✅ Page shows tabs: Pending, Approved, Rejected
- ✅ "Pending" tab selected by default with count badge
- ✅ Each tab has list of leaves
```

### View Pending Leaves

**Test Case:** View leaves awaiting approval

```
1. On leave-approval page, Pending tab selected
2. View list

Expected:
- [ ] Each leave shows as card with:
  - Employee name
  - Date range (e.g., "Feb 10 - Feb 12, 2025")
  - Total days (e.g., "3 hari")
  - Status badge: "PENDING" (yellow)
  - Reason (if provided)
  - "Approve" button (green)
  - "Reject" button (red)
```

### Approve Leave

**Test Case:** Boss approves leave

```
1. On Pending leaves tab
2. Find a leave
3. Click "Approve" button

Expected:
- ✅ Confirmation dialog appears
- ✅ Dialog: "Setujui pengajuan cuti X hari?"
- ✅ On confirm, leave status changes to "Approved"
- ✅ Move to "Approved" tab
- ✅ Employee receives notification
- ✅ Check employee dashboard → leave days updated
```

### Reject Leave

**Test Case:** Boss rejects leave with reason

```
1. On Pending leaves tab
2. Click "Reject" button

Expected:
- ✅ Modal dialog appears: "Alasan Penolakan"
- ✅ Shows textarea for rejection reason
- [ ] Type reason: "Diperlukan untuk operasional"
- ✅ Click "Reject" button
- ✅ Leave moves to "Rejected" tab
- ✅ Employee receives notification with reason
```

### View Approved/Rejected History

**Test Case:** View past approvals

```
1. Click "Approved" tab

Expected:
- ✅ Shows all approved leaves for employee
- [ ] Each shows: name, dates, days, status "APPROVED"
- [ ] Shows approval date/time
- [ ] Shows approver name (optional)
- [ ] No action buttons (read-only)

2. Click "Rejected" tab

Expected:
- ✅ Shows rejected leaves
- [ ] Each shows: name, dates, days, status "REJECTED"
- [ ] Shows rejection date/time
- [ ] Shows rejection reason
- [ ] No action buttons (read-only)
```

---

## 9️⃣ Deposit Approval Testing

### Access Deposit Approval

**Test Case:** Boss verifies deposits

```
1. Login as boss
2. Click "Verifikasi Setor" or navigate to /boss/deposit-approval

Expected:
- ✅ Page shows pending deposits
- ✅ Page title: "Verifikasi Setor Beras" or similar
- ✅ No tabs (single view of pending only)
```

### View Pending Deposits

**Test Case:** View deposits awaiting verification

```
1. On deposit-approval page
2. View grid of deposits

Expected:
- [ ] Each deposit card shows:
  - Employee name & email
  - Tanggal setor
  - Kg amount (orange)
  - Total harga (green)
  - Status badge: "PENDING" (yellow)
  - Photo thumbnail
  - "Lihat Foto" link or preview
  - "Verifikasi" button (green)
  - "Tolak" button (red)
```

### View Deposit Photo

**Test Case:** Preview deposit photo

```
1. Click "Lihat Foto" or click photo thumbnail
2. Photo should open (lightbox or modal)

Expected:
- ✅ Photo displays full-size
- ✅ Can see rice evidence
- ✅ Can close and go back
```

### Verify Deposit

**Test Case:** Boss verifies deposit

```
1. Click "Verifikasi" button on deposit

Expected:
- ✅ Confirmation dialog: "Verifikasi setor X kg?"
- ✅ On confirm, deposit status → "Verified"
- ✅ Employee receives notification
- ✅ Deposit moves out of pending list
- ✅ Check monthly summary: kg increased, salary calculated
```

### Reject Deposit

**Test Case:** Boss rejects deposit with reason

```
1. Click "Tolak" button on deposit

Expected:
- ✅ Modal appears: "Alasan Penolakan"
- [ ] Type reason: "Kualitas beras tidak sesuai standar"
- ✅ Click "Tolak" button
- ✅ Deposit status → "Rejected"
- ✅ Employee receives notification with reason
- ✅ Deposit removed from pending list
```

### Check Verified Deposits Impact

**Test Case:** Verified deposits affect salary

```
1. Verify deposit: 50 kg at Rp 15,000/kg = Rp 750,000
2. Go to employee detail page
3. Check monthly stats

Expected:
- ✅ "Total Kg Setor": now includes 50 kg
- ✅ "Gaji": now shows Rp 750,000 (or more if multiple deposits)
- ✅ Check monthly_summaries table in DB: total_kg_deposited=50, total_salary=750000
```

---

## 🔟 Payroll Settings Testing

### Access Settings

**Test Case:** Boss views payroll settings

```
1. Login as boss
2. Click "Pengaturan" button or navigate to /boss/payroll-settings

Expected:
- ✅ Page shows settings form
- ✅ Form fields visible:
  - [ ] "Upah per Kg Beras" (Rp input)
  - [ ] "Latitude Kantor" (decimal)
  - [ ] "Longitude Kantor" (decimal)
  - [ ] "Max Jarak dari Kantor" (km)
  - [ ] "Max Hari Cuti per Bulan" (days)
  - [ ] "Min Setor per Minggu" (kg)
- ✅ Current values displayed
```

### Update Settings

**Test Case:** Boss changes payroll configuration

```
1. On settings page
2. Change "Upah per Kg Beras" from 15000 to 16000
3. Click "Simpan"

Expected:
- ✅ Confirmation: "Simpan pengaturan?"
- ✅ On confirm, settings updated
- ✅ Success message: "Pengaturan berhasil disimpan"
- ✅ Page reloads with new values
- ✅ Future deposits calculated with new price
```

### Validate Settings

**Test Case:** Form validation

```
1. On settings page
2. Try invalid inputs:
   - Harga = 0 → Error: "must be > 0"
   - Max jarak = negative → Error
   - Cuti days = 0 → Error
3. Try valid inputs:
   - Harga = 20000 ✅
   - Max distance = 2.5 ✅
   - Cuti = 5 ✅

Expected:
- ✅ All validations work client-side and server-side
```

### GPS Settings Impact

**Test Case:** Changing office location

```
1. Change "Latitude Kantor" and "Longitude Kantor"
2. Save settings
3. Have employee check-in
4. Check-in form now shows new office location

Expected:
- ✅ Google Maps centers on new location
- ✅ Distance calculation uses new center
- ✅ Employee's distance recalculated correctly
```

---

## 1️⃣1️⃣ Notifications Testing

### Real-time Notifications

**Test Case:** Notifications appear in real-time

````
1. Login as boss in one browser/tab
2. Login as karyawan1 in another browser/tab
3. In employee tab:
   - Create leave request
   - Submit setor beras
4. In boss tab:
   - Look at notification bell
   - Or check API: /api/notifications

Expected:
- ✅ Boss dashboard shows new pending items
- ✅ Notification dropdown (API) updates every 30 seconds
- ✅ Count badge increments
- ✅ Can see notification details

Verify DB:
```sql
SELECT * FROM notifications WHERE user_id = 1 ORDER BY created_at DESC;
````

```

### Mark Notification as Read
**Test Case:** Click notification
```

1. In boss tab, click bell icon
2. Click a notification
3. Or manually test API

Expected:

- ✅ Notification marked as read
- ✅ Check API again: notification count decreases
- [ ] Test API: POST /api/notifications/{id}/read

```

---

## 1️⃣2️⃣ Responsive Design Testing

### Mobile View (375px width)
**Test Case:** UI works on mobile
```

1. Open application
2. Open Browser DevTools (F12)
3. Toggle Device Toolbar
4. Select iPhone 12 Pro (390px) or iPhone SE (375px)

Test each page:

- [ ] Login page → Form readable, buttons clickable
- [ ] Dashboard → Cards stack vertically
- [ ] Forms → Inputs fit screen, labels visible
- [ ] Tables → Horizontal scroll or responsive columns
- [ ] Navbar → Hamburger menu works
- [ ] Sidebar → Collapsible on mobile

Expected:

- ✅ No horizontal scroll (unless needed)
- ✅ All text readable
- ✅ All buttons touchable (48px min height)
- ✅ Images scale properly
- ✅ Forms work (date pickers, uploads, etc)

```

### Tablet View (768px width)
**Test Case:** UI works on tablet
```

1. Select iPad (768px)
2. Repeat above checks

Expected:

- ✅ Two-column layout works
- ✅ Tables have more columns visible
- ✅ Form fields wider

```

### Desktop View (1920px width)
**Test Case:** UI works on large screen
```

1. Full screen browser
2. Resize to 1920px width

Expected:

- ✅ Three-column layouts work
- ✅ Full table display
- ✅ No wasted space

```

---

## 1️⃣3️⃣ Design & UI Testing

### Colors & Gradients
**Test Case:** Visual design correct
```

1. Check each page for colors:
    - [ ] Primary blue: #667eea
    - [ ] Secondary: #764ba2
    - [ ] Green: #10B981
    - [ ] Red: #EF4444
    - [ ] Yellow: #F59E0B
    - [ ] Orange: #F97316

Verify gradients:

- [ ] Buttons have gradient backgrounds
- [ ] Backgrounds smooth color transitions

Expected:

- ✅ Colors match design specs
- ✅ No jarring color clashes
- ✅ Good contrast for readability

```

### Icons
**Test Case:** Font Awesome icons display
```

1. On each page, count icons visible:
    - [ ] Sidebar icons (menu items)
    - [ ] Button icons
    - [ ] Status icons
    - [ ] Alert icons

Go to: Check Network tab (DevTools)

- [ ] Font Awesome CSS loaded
- [ ] Font files loaded successfully

Expected:

- ✅ All icons show correctly
- ✅ No missing/broken icons
- ✅ Icons have correct color
- ✅ Icons align properly with text

```

### Typography
**Test Case:** Text formatting
```

1. Check headings:
    - [ ] h1, h2, h3, h4 sizes different
    - [ ] Font weight varies (bold for headers)

2. Check body text:
    - [ ] Line height readable (not cramped)
    - [ ] Font family consistent (probably sans-serif)
    - [ ] Contrast sufficient for accessibility

Expected:

- ✅ Hierarchy clear (bigger headers stand out)
- ✅ Text easily readable
- ✅ Consistent styling throughout

```

### Buttons & Forms
**Test Case:** UI elements look polished
```

1. Check buttons:
    - [ ] Hover effect (color change, shadow)
    - [ ] Click feedback (press animation)
    - [ ] Disabled state (greyed out)

2. Check form inputs:
    - [ ] Focus state (border highlight, shadow)
    - [ ] Placeholder text visible
    - [ ] Error messages red/prominent
    - [ ] Success messages green/prominent

3. Check modals:
    - [ ] Backdrop dimmed
    - [ ] Modal centered
    - [ ] Close button works
    - [ ] Click outside closes (if enabled)

Expected:

- ✅ All interactive elements have feedback
- ✅ Professional appearance
- ✅ Smooth transitions (not abrupt)

```

---

## 1️⃣4️⃣ Data Integrity Testing

### Concurrent Operations
**Test Case:** Multiple users simultaneous actions
```

1. Open 2 browser tabs: one as karyawan1, one as karyawan2
2. Both try to check-in at same time
3. Both try to submit deposits

Expected:

- ✅ Both succeed with own data
- ✅ Database shows both records
- ✅ No data corruption
- ✅ Timestamps are different but both recorded

```

### Data Consistency
**Test Case:** Check calculations are consistent
```

1. Manually calculate:
    - Leave days: (end - start) + 1 = X
    - Salary: kg × price = Y
    - Distance: Haversine(lat1, lon1, lat2, lon2) = Z

2. Check database values match calculations

Expected:

- ✅ All calculations accurate
- ✅ No rounding errors (unless specified)
- ✅ Foreign keys valid
- ✅ No orphaned records

```

### Transaction Safety
**Test Case:** Incomplete operations don't corrupt data
```

1. During form submit, kill the request (F12 Network tab, abort)
2. Refresh page

Expected:

- ✅ Data not partially saved
- ✅ Form state reset
- ✅ No dangling records in DB
- ✅ Error message shown to user

```

---

## 1️⃣5️⃣ Performance Testing

### Page Load Times
**Test Case:** Pages load reasonably fast
```

1. Open DevTools → Network tab
2. Load each page with "Slow 3G" or normal
3. Check load times:
    - [ ] Dashboard: < 3 seconds
    - [ ] Forms: < 2 seconds
    - [ ] Lists: < 3 seconds

Expected:

- ✅ Fast enough for user patience
- ✅ No freezing UI
- ✅ Loading indicators shown for slow requests

```

### Database Query Count
**Test Case:** N+1 queries avoided
```

1. In Laravel, enable query logging
2. Load dashboard
3. Check SQL queries in logs

Expected:

- ✅ Reasonable query count (< 10 for simple pages)
- ✅ No repeated queries (use eager loading)
- ✅ No missing indexes shown in EXPLAIN

```

### File Upload Performance
**Test Case:** Uploads complete quickly
```

1. Upload 5MB image as deposit photo
2. Should complete in < 5 seconds

Expected:

- ✅ Upload progress shown
- ✅ Completes without timeout
- ✅ File appears in storage
- ✅ Accessible via URL

```

---

## 1️⃣6️⃣ Error Handling Testing

### 404 Errors
**Test Case:** Non-existent routes
```

1. Go to: http://localhost:8000/non-existent-page
2. Go to: http://localhost:8000/boss/employees/999

Expected:

- ✅ Shows 404 error page (or 403 if unauthorized)
- ✅ Friendly error message
- ✅ Link to go back
- ✅ Not generic server error

```

### 500 Errors
**Test Case:** Server errors handled gracefully
```

1. Intentionally trigger error (in local testing only)
2. Check error response

Expected:

- ✅ Shows friendly error message (not raw exception)
- ✅ User not exposed to sensitive info
- ✅ Logging captures error for debugging

```

### Validation Errors
**Test Case:** Form validation shows helpful messages
```

1. Submit forms with invalid data
2. Check error messages

Expected:

- ✅ Errors show in red/prominent
- ✅ Specific error per field (not generic)
- ✅ Helpful suggestion to fix
- ✅ Form doesn't submit with errors

```

---

## ✅ Final QA Checklist

- [ ] All authentication scenarios pass
- [ ] Employee workflow works end-to-end
- [ ] Boss approval workflow works
- [ ] GPS & face recognition functional
- [ ] All calculations correct (distance, salary, days)
- [ ] Database integrity maintained
- [ ] No console JavaScript errors
- [ ] No server 500 errors
- [ ] All forms validate properly
- [ ] Responsive on mobile/tablet/desktop
- [ ] Colors & icons display correctly
- [ ] Notifications work in real-time
- [ ] Performance acceptable
- [ ] Security: cannot access other role's pages
- [ ] Documentation complete & accurate

---

## 🐛 Bug Reporting Template

If you find an issue, document it:

```

**Bug Title:** [Short description]
**Severity:** Critical / High / Medium / Low
**Steps to Reproduce:**

1. ...
2. ...
3. ...

**Expected Result:**
[What should happen]

**Actual Result:**
[What actually happened]

**Screenshots/Video:**
[Attach if possible]

**Browser/Device:**
[Chrome, Firefox, iPhone, etc]

**Environment:**
[localhost, staging, production]

```

---

**Last Updated:** 2025
**Status:** Ready for Testing
**Next:** Start with Authentication Testing → Dashboard → Employee Features → Boss Features
```
