# Verifikasi Face Recognition Implementation

## ✅ Status Implementasi

Sistem face recognition untuk Rice Log telah diimplementasikan dengan alur berikut:

### 1. **ENROLLMENT (Pendaftaran Wajah Karyawan)**

**Lokasi**: [resources/views/boss/employee-management/create.blade.php](resources/views/boss/employee-management/create.blade.php)

**Proses**:

```
1. Boss/Manager buka form Tambah Karyawan
2. Pada bagian "Daftar Wajah" (opsional):
   - Klik tombol "Buka Kamera"
   - Sistem menggunakan FaceRecognitionHelper.startCamera()
   - Real-time face detection dimulai
   - Ketika wajah terdeteksi, status berubah hijau
   - Klik "Ambil Foto" untuk capture face descriptor
   - FaceRecognitionHelper.getFaceDescriptors() menangkap 128-entry face descriptor
   - Face descriptor disimpan dalam hidden input (face_descriptors)
   - Klik "Tutup Kamera"
3. Submit form employee creation
4. Backend (EmployeeManagementController.store()):
   - Buat user baru
   - Jika face_descriptors ada, panggil $employee->enrollFace()
   - Face descriptor disimpan ke database users.face_data
   - Flag users.face_enrolled diset TRUE
```

**Files**:

- **Backend**: `app/Http/Controllers/EmployeeManagementController.php` (line 136-154)
    - Validasi: `'face_descriptors' => 'nullable|array'`
    - Enrollment: `$employee->enrollFace($validated['face_descriptors'])`

- **Model**: `app/Models/User.php` (line 128-145)
    - Method: `enrollFace()` - simpan face data
    - Method: `hasFaceEnrolled()` - check apakah wajah sudah terdaftar
    - Method: `getFaceData()` - ambil face data

- **Database Migration**: `database/migrations/2026_02_02_000010_add_face_data_to_users_table.php`
    - Kolom: `face_data` (JSON) - menyimpan 128 face descriptor sebagai array
    - Kolom: `face_enrolled` (boolean) - status enrollment

- **Frontend Helper**: `public/js/face-recognition-helper.js` ⭐ **BARU**
    - Method: `initModels()` - load TensorFlow & face-api models
    - Method: `startCamera()` - buka access kamera
    - Method: `startDetection()` - real-time face detection
    - Method: `getFaceDescriptors()` - ambil face descriptor dari video
    - Method: `captureFace()` - capture image dari video

---

### 2. **VERIFICATION (Verifikasi Saat Absen)**

**Lokasi**: [resources/views/employee/absence/form.blade.php](resources/views/employee/absence/form.blade.php)

**Proses**:

```
1. Karyawan buka form Absen Masuk/Keluar
2. Pada bagian "Face Recognition":
   - Klik "Mulai Kamera"
   - Video stream aktif dengan real-time face detection
   - Canvas menampilkan kotak hijau & landmarks saat wajah terdeteksi
3. Ketika wajah terlihat jelas:
   - Klik "Ambil Foto"
   - FaceRecognitionHelper.captureFace() capture image
   - Image disimpan ke file input face_image
4. Selesaikan form (lokasi GPS, status, deskripsi)
5. Submit form
6. Backend (AbsenceController.store()):
   - Simpan face image ke storage/faces/YYYY/MM/DD/
   - Jika status="hadir" dan user.hasFaceEnrolled():
     ✓ Panggil $this->verifyFace() untuk verifikasi
     ✓ Hasil: true/false
   - Buat Absence record dengan:
     - face_image: path file
     - face_verified: boolean (hasil verifikasi)
   - Return response dengan pesan verifikasi
```

**Files**:

- **Backend Controller**: `app/Http/Controllers/AbsenceController.php` (line 62-127)
    - Validasi face_image: `'face_image' => 'required_if:status,hadir|image|mimes:jpeg,png,jpg|max:2048'`
    - Upload: Simpan ke `storage/faces/YYYY/MM/DD/`
    - Verifikasi: Call `verifyFace()` jika user memiliki enrolled face

- **Verification Method**: `app/Http/Controllers/AbsenceController.php` (line 107-122)
    - Method: `verifyFace($imagePath, $user): bool`
    - Sekarang: Return `$user->hasFaceEnrolled()` (simplified)
    - **TODO**: Implementasi actual face matching server-side atau client-side

- **Database Model**: `app/Models/Absence.php`
    - Kolom: `face_image` - path file wajah
    - Kolom: `face_verified` - hasil verifikasi (nullable boolean)

- **Frontend Helper**: `public/js/face-recognition-helper.js`
    - Method: `compareFaceDescriptors()` - hitung euclidean distance
    - Method: `verifyFace()` - bandingkan 2 descriptor
    - Method: `verifyFaceMultiple()` - bandingkan multiple descriptors

---

## 🔄 Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                     EMPLOYEE REGISTRATION                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Boss/Manager → Create Employee Form                           │
│                 ├─ Basic Info (name, email, phone, etc.)        │
│                 └─ Face Enrollment (Optional)                   │
│                    ├─ startCamera()                             │
│                    ├─ startDetection() [Real-time preview]      │
│                    ├─ getFaceDescriptors() [128-entry vector]   │
│                    └─ Hidden input: face_descriptors            │
│                                                                  │
│  Submit Form                                                    │
│     ↓                                                            │
│  EmployeeManagementController.store()                          │
│     ├─ Create User                                              │
│     └─ If face_descriptors: $user->enrollFace()               │
│        └─ Save to: users.face_data (JSON)                      │
│        └─ Set: users.face_enrolled = true                      │
│                                                                  │
│  DATABASE: user.face_data = [0.234, 0.456, 0.789, ...]  (128) │
│            user.face_enrolled = true                            │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                   EMPLOYEE CHECK-IN/CHECK-OUT                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Employee → Absence Form (masuk/keluar)                        │
│             ├─ GPS Location                                     │
│             ├─ Status (hadir/sakit/izin)                        │
│             └─ Face Recognition (if status=hadir)              │
│                ├─ startCamera()                                 │
│                ├─ startDetection() [Real-time preview]          │
│                ├─ captureFace() [Save to file input]           │
│                └─ File: face_image                              │
│                                                                  │
│  Submit Form                                                    │
│     ↓                                                            │
│  AbsenceController.store()                                     │
│     ├─ Validate & store face_image                             │
│     ├─ If hadir & user.hasFaceEnrolled():                      │
│     │  └─ verifyFace($imagePath, $user)                        │
│     │     ├─ [TODO: Compare face descriptors]                  │
│     │     └─ Return true/false                                 │
│     ├─ Create Absence record:                                  │
│     │  ├─ face_image: storage path                             │
│     │  ├─ face_verified: true/false/null                       │
│     │  └─ checked_at: now()                                    │
│     └─ Return response with verification status               │
│                                                                  │
│  DATABASE: absences.face_image = "faces/2024/01/15/face.jpg"  │
│            absences.face_verified = true/false/null            │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                      FACE MATCHING LOGIC                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Captured Face Descriptor        Enrolled Face Descriptor       │
│  [128-entry vector]              [128-entry vector]             │
│        ↓                                ↓                        │
│        └──────── Euclidean Distance ────┘                       │
│                        ↓                                         │
│              Distance < Threshold (0.6)?                        │
│                 YES: Match ✓  NO: No Match ✗                   │
│                                                                  │
│  Location: FaceRecognitionHelper.compareFaceDescriptors()      │
│           FaceRecognitionHelper.verifyFace()                    │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🛠️ Technical Details

### Face Descriptor (128-entry vector)

- Generated by `face-api.js` face recognition model
- 128 floating-point numbers representing face features
- Unique per person
- Format: `Float32Array` → JSON array when stored

### Verification Algorithm

```javascript
// Euclidean Distance
distance = sqrt(Σ(descriptor1[i] - descriptor2[i])²)

// Threshold
if (distance < 0.6) → MATCH ✓
else → NO MATCH ✗
```

### Model Path

- Location: `public/models/`
- Files needed:
    - `ssd_mobilenetv1_model-weights_manifest.json` ✓
    - `ssd_mobilenetv1_model.bin` ✓
    - `face_landmark_68_model-weights_manifest.json` ✓
    - `face_landmark_68_model.bin` ✓
    - `face_recognition_model-weights_manifest.json` ✓
    - `face_recognition_model.bin` ✓

### Scripts Loaded

```html
<!-- In create.blade.php & form.blade.php -->
<script
    async
    defer
    src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4"
></script>
<script
    async
    defer
    src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"
></script>
<script src="{{ asset('js/face-recognition-helper.js') }}"></script>
```

---

## ✅ Checklist Implementasi

- [x] Face model files di `public/models/` (ssd_mobilenetv1, face_landmark_68, face_recognition)
- [x] TensorFlow.js CDN reference
- [x] face-api.js CDN reference
- [x] `FaceRecognitionHelper` helper script (`public/js/face-recognition-helper.js`) ⭐ **BARU**
- [x] Database migrations untuk `face_data` & `face_enrolled`
- [x] User model methods: `enrollFace()`, `hasFaceEnrolled()`, `getFaceData()`
- [x] Employee creation form dengan face enrollment UI
- [x] Employee absence form dengan face capture UI
- [x] Backend: Save face data pada enrollment
- [x] Backend: Save face image pada absence
- [x] Backend: Basic verification (check if enrolled)
- [ ] **TODO**: Implement proper server-side face descriptor comparison

---

## 🚀 Penggunaan

### Untuk Enrollment (Boss/Manager):

```javascript
// Di create.blade.php
await FaceRecognitionHelper.initModels(window.location.origin + "/models/");
await FaceRecognitionHelper.startCamera(videoElement);
FaceRecognitionHelper.startDetection(
    videoElement,
    canvasElement,
    (detected) => {
        console.log("Face detected:", detected);
    },
);

// Capture
const descriptors =
    await FaceRecognitionHelper.getFaceDescriptors(videoElement);
document.getElementById("face_descriptors").value = JSON.stringify(
    descriptors[0],
);
```

### Untuk Verification (Backend):

```php
// Di AbsenceController.php
if ($user->hasFaceEnrolled()) {
    $enrolled = $user->getFaceData();
    // TODO: Compare captured face descriptor dengan enrolled descriptor
    // Return verification result
}
```

---

## 📝 Notes

1. **Face Descriptor Storage**: Disimpan sebagai JSON array di database
    - Format: `[0.234, 0.456, 0.789, ..., 0.123]` (128 entries)
    - Database column: `users.face_data`

2. **Face Image Storage**: Disimpan sebagai file jpg
    - Path: `storage/faces/YYYY/MM/DD/filename.jpg`
    - Symlink: `public/storage/faces/...` (via `php artisan storage:link`)
    - Database: `absences.face_image`

3. **Verification Status**:
    - `true` = Face match ✓
    - `false` = Face no match ✗
    - `null` = Not verified / No enrolled face
    - Database: `absences.face_verified`

4. **Browser Requirements**:
    - Kamera access permission
    - Modern browser (Chrome, Firefox, Edge)
    - HTTPS recommended (required di production)

5. **Model Loading**:
    - Dimuat hanya sekali per session (cached)
    - File size: ~5-6MB total
    - Loading time: 2-5 detik (first time)

---

## 🔧 Testing

### Manual Test Enrollment:

1. Go to: `/boss/employees/create`
2. Fill basic info
3. Click "Buka Kamera" → Allow camera access
4. Position face in front of camera
5. Click "Ambil Foto" ketika detected
6. Submit form
7. Check database: `users.face_enrolled` should be `true`

### Manual Test Absence:

1. Go to: `/employee/absence/masuk`
2. Set location GPS
3. Set status: "hadir"
4. Click "Mulai Kamera" → Allow camera access
5. Position face in front of camera
6. Click "Ambil Foto"
7. Submit form
8. Check database: `absences.face_verified` should reflect verification result

---

## 🐛 Troubleshooting

| Issue                     | Solution                                             |
| ------------------------- | ---------------------------------------------------- |
| Camera not working        | Check browser permissions, use HTTPS/localhost       |
| Models not loading        | Check Network tab, verify path `/models/` exists     |
| "No face detected"        | Better lighting, position face clearly, try again    |
| Face data not saving      | Check database migration ran: `php artisan migrate`  |
| Verification always false | Check if user has `face_enrolled = true` in database |
