# 🎯 QUICK REFERENCE - FACE RECOGNITION FLOW

## 📊 System Architecture

```
┌──────────────────────────────────────────────────────────────────────────┐
│                        RICE LOG FACE RECOGNITION SYSTEM                  │
└──────────────────────────────────────────────────────────────────────────┘

                          ┌─────────────────────┐
                          │   TENSORFLOW.JS     │
                          │   face-api.js 0.22  │
                          │   (TinyFaceDetector)│
                          │   (FaceLandmark68)  │
                          │   (FaceRecognition) │
                          └─────────────────────┘
                                    │
                                    ▼
                      ┌───────────────────────────┐
                      │ FaceRecognitionHelper.js  │
                      │                           │
                      │ ✓ initModels()            │
                      │ ✓ startCamera()           │
                      │ ✓ startDetection()        │
                      │ ✓ getFaceDescriptors()    │
                      │ ✓ captureFace()           │
                      │ ✓ verifyFace()            │
                      │ ✓ compareFaceDescriptors()│
                      └───────────────────────────┘
                            │            │
                ┌───────────┘            └─────────────┐
                │                                      │
                ▼                                      ▼
    ┌──────────────────────┐          ┌──────────────────────────┐
    │ ENROLLMENT FLOW      │          │ VERIFICATION FLOW        │
    │ (New Employee)       │          │ (Check-in/Check-out)     │
    │                      │          │                          │
    │ 1. startCamera()     │          │ 1. startCamera()         │
    │ 2. startDetection()  │          │ 2. startDetection()      │
    │ 3. captureFace()     │          │ 3. captureFace()         │
    │ 4. getFaceDesc()     │          │ 4. Send to server        │
    │ 5. JSON.stringify()  │          │                          │
    │                      │          │                          │
    │ → hidden input       │          │ → file input             │
    │ → form submit        │          │ → form submit            │
    └──────────────────────┘          └──────────────────────────┘
                │                                      │
                │                                      │
                ▼                                      ▼
    ┌──────────────────────┐          ┌──────────────────────────┐
    │  BACKEND PROCESSING  │          │  BACKEND PROCESSING      │
    │  (EmployeeController)│          │  (AbsenceController)     │
    │                      │          │                          │
    │ 1. Validate face_desc│          │ 1. Validate face_image   │
    │ 2. User::create()    │          │ 2. Store image           │
    │ 3. enrollFace()      │          │ 3. verifyFace()          │
    └──────────────────────┘          └──────────────────────────┘
                │                                      │
                ▼                                      ▼
    ┌──────────────────────┐          ┌──────────────────────────┐
    │   DATABASE STORAGE   │          │   DATABASE STORAGE       │
    │                      │          │                          │
    │ users.face_data      │          │ absences.face_image      │
    │ [0.23, 0.45, ...]    │          │ "faces/2026/02/04/..."   │
    │                      │          │                          │
    │ users.face_enrolled  │          │ absences.face_verified   │
    │ true                 │          │ true/false/null          │
    └──────────────────────┘          └──────────────────────────┘
```

---

## 🔄 ENROLLMENT SEQUENCE

```
EMPLOYEE REGISTRATION
│
├─ Boss → Admin Panel → Employees → Add New
│
├─ FORM LOAD
│  ├─ FaceRecognitionHelper.initModels('/models/')
│  └─ Load TensorFlow + face-api models
│
├─ OPEN CAMERA
│  ├─ Click "Buka Kamera"
│  ├─ FaceRecognitionHelper.startCamera(videoElement)
│  └─ Request camera permission
│
├─ REAL-TIME DETECTION
│  ├─ FaceRecognitionHelper.startDetection(video, canvas, callback)
│  ├─ Canvas shows:
│  │  ├─ Green bounding box around face
│  │  ├─ Red dots for landmarks (68 points)
│  │  └─ Status text: "Wajah terdeteksi!"
│  └─ Callback: (faceDetected: boolean) => void
│
├─ CAPTURE FACE
│  ├─ Click "Ambil Foto"
│  ├─ FaceRecognitionHelper.getFaceDescriptors(videoElement)
│  │  └─ Returns: Float32Array[] (1 per detected face)
│  │  └─ Each has 128 entries
│  ├─ Get first descriptor: descriptors[0]
│  └─ Store to hidden input: face_descriptors = JSON.stringify(desc)
│
├─ CLOSE CAMERA
│  ├─ Click "Tutup Kamera"
│  ├─ FaceRecognitionHelper.stopDetection()
│  ├─ FaceRecognitionHelper.stopCamera(videoElement)
│  └─ Success message shown
│
├─ SUBMIT FORM
│  ├─ Validate all fields
│  ├─ POST to: /boss/employees
│  └─ Include face_descriptors in request
│
├─ BACKEND PROCESSING
│  ├─ EmployeeManagementController::store()
│  ├─ Validate: 'face_descriptors' => 'nullable|array'
│  ├─ Create User
│  ├─ If face_descriptors provided:
│  │  ├─ $user->enrollFace($descriptors)
│  │  ├─ Update: users.face_data = JSON($descriptors)
│  │  └─ Set: users.face_enrolled = true
│  └─ Return success
│
└─ DATABASE STATE
   ├─ users.face_data = [0.234, 0.456, 0.789, ...] (128 entries)
   └─ users.face_enrolled = true
```

---

## ✓ VERIFICATION SEQUENCE

```
EMPLOYEE CHECK-IN/CHECK-OUT
│
├─ Employee → Absence → Masuk/Keluar
│
├─ FORM LOAD
│  ├─ FaceRecognitionHelper.initModels('/models/')
│  ├─ Show GPS location input
│  ├─ Show status selector (hadir/sakit/izin)
│  └─ Show face recognition section
│
├─ ENTER DETAILS
│  ├─ GPS: Auto-fill via Geolocation API
│  ├─ Status: Select "hadir" (face required)
│  └─ Description: Optional (if sakit/izin)
│
├─ OPEN CAMERA
│  ├─ Click "Mulai Kamera"
│  ├─ FaceRecognitionHelper.startCamera(videoElement)
│  └─ Request camera permission
│
├─ REAL-TIME DETECTION
│  ├─ FaceRecognitionHelper.startDetection(video, canvas, callback)
│  ├─ Canvas overlay with:
│  │  ├─ Green box when face detected
│  │  ├─ Red landmarks
│  │  └─ Status text
│  └─ Callback updates status in real-time
│
├─ CAPTURE FACE
│  ├─ Click "Ambil Foto"
│  ├─ FaceRecognitionHelper.captureFace(videoElement)
│  │  └─ Returns: Blob (JPEG image)
│  ├─ Create File object from blob
│  ├─ Set to file input: face_image
│  └─ Show captured preview
│
├─ CLOSE CAMERA
│  ├─ Click "Tutup Kamera"
│  ├─ FaceRecognitionHelper.stopDetection()
│  ├─ FaceRecognitionHelper.stopCamera()
│  └─ Video element hidden
│
├─ SUBMIT FORM
│  ├─ Validate GPS
│  ├─ Validate status
│  ├─ Validate face_image (required for hadir)
│  ├─ POST to: /employee/absence/store
│  └─ Include face_image file
│
├─ BACKEND PROCESSING
│  ├─ AbsenceController::store()
│  ├─ Validate face_image
│  ├─ If status = "hadir" && user.face_enrolled:
│  │  ├─ Store image: $file->store('faces/Y/m/d', 'public')
│  │  │  └─ Result: "faces/2026/02/04/xyz123.jpg"
│  │  ├─ Call: $this->verifyFace($imagePath, $user)
│  │  │  ├─ Currently: Just checks if enrolled
│  │  │  ├─ TODO: Extract descriptor & compare
│  │  │  └─ Return: true/false
│  │  └─ Set: face_verified = $result
│  ├─ Create Absence record:
│  │  ├─ user_id: auth()->id()
│  │  ├─ type: 'masuk' or 'keluar'
│  │  ├─ status: 'hadir' or 'sakit' or 'izin'
│  │  ├─ face_image: storage path
│  │  ├─ face_verified: true/false/null
│  │  └─ checked_at: now()
│  ├─ Update: users.last_presence_at = now()
│  └─ Return response
│
├─ RESPONSE MESSAGE
│  ├─ "Absen masuk berhasil! Wajah Anda telah terdeteksi."
│  ├─ If face_verified:
│  │  └─ " Wajah terverifikasi."
│  └─ If not verified:
│     └─ " (Wajah tidak match - perlu verifikasi manual)."
│
└─ DATABASE STATE
   ├─ absences.face_image = "faces/2026/02/04/abc123.jpg"
   ├─ absences.face_verified = true/false
   ├─ storage/app/public/faces/2026/02/04/abc123.jpg = [image file]
   └─ public/storage → symlink → storage/app/public
```

---

## 🔢 FACE DESCRIPTOR

```
What is a Face Descriptor?
├─ 128-entry floating-point vector
├─ Generated by face recognition neural network
├─ Represents unique facial features
├─ Format: Float32Array in JavaScript
├─ Format: JSON array in database
│
Example:
├─ [0.234, 0.456, 0.789, 0.123, ..., 0.567]
├─ Length: Always 128 entries
├─ Range: Each value typically 0.0 - 1.0
│
How it works:
├─ Input: Face image (face cropped from full image)
├─ Process: Neural network extracts features
├─ Output: 128-dimensional vector
├─ Property: Same person = similar descriptor
├─ Property: Different person = different descriptor
│
Similarity check (Euclidean Distance):
├─ distance = sqrt(Σ(desc1[i] - desc2[i])²)
├─ If distance < 0.6: MATCH ✓
├─ If distance ≥ 0.6: NO MATCH ✗
│
Threshold tuning:
├─ Lower threshold (e.g., 0.4): Stricter matching
├─ Higher threshold (e.g., 0.8): Lenient matching
├─ Default: 0.6 (good balance)
└─ Adjust based on your needs
```

---

## 📁 FILE LOCATIONS

```
Frontend:
├─ public/js/face-recognition-helper.js
│  └─ All face recognition JavaScript functions
│
├─ resources/views/boss/employee-management/create.blade.php
│  └─ Lines 1-10: Script includes
│  └─ Lines 105-150: Face enrollment UI
│  └─ Lines 218-424: JavaScript enrollment logic
│
├─ resources/views/employee/absence/form.blade.php
│  └─ Lines 1-15: Script includes
│  └─ Lines 145-175: Face capture UI
│  └─ Lines 250-430: JavaScript capture & detection logic
│
Backend:
├─ app/Http/Controllers/EmployeeManagementController.php
│  └─ Lines 136-154: enrollFace() processing
│
├─ app/Http/Controllers/AbsenceController.php
│  └─ Lines 62-75: Face image storage
│  └─ Lines 77-79: Verification call
│  └─ Lines 107-165: verifyFace() method
│  └─ Lines 167-184: calculateFaceDistance() (PHP)
│
├─ app/Models/User.php
│  └─ Lines 31-35: fillable attributes
│  └─ Lines 60-62: face_data & face_enrolled casts
│  └─ Lines 128-145: Face methods
│
Database:
├─ database/migrations/2026_02_02_000010_add_face_data_to_users_table.php
│  └─ Creates: users.face_data (JSON)
│  └─ Creates: users.face_enrolled (boolean)
│
├─ app/Models/Absence.php
│  └─ Columns: face_image, face_verified
│
Documentation:
├─ FACE_RECOGNITION_VERIFICATION.md
│  └─ Detailed technical documentation
│
├─ FACE_RECOGNITION_COMPLETE_SETUP.md
│  └─ Complete setup guide & checklist
│
├─ FACE_RECOGNITION_QUICK_REFERENCE.md (this file)
│  └─ Quick diagrams & flows

Storage:
├─ public/models/
│  └─ ssd_mobilenetv1_model-weights_manifest.json
│  └─ ssd_mobilenetv1_model.bin
│  └─ face_landmark_68_model-weights_manifest.json
│  └─ face_landmark_68_model.bin
│  └─ face_recognition_model-weights_manifest.json
│  └─ face_recognition_model.bin
│
└─ storage/app/public/faces/YYYY/MM/DD/
   └─ Captured face images
```

---

## ✅ CHECKLIST FOR TESTING

```
PRE-TESTING
□ Run: php artisan migrate (ensure schema updated)
□ Check: public/models/ has all 6 files
□ Check: public/js/face-recognition-helper.js exists
□ Browser: Chrome, Firefox, or Edge (all support WebRTC)

ENROLLMENT TEST
□ Open: http://localhost:8000/boss/employees/create
□ Fill: Basic employee info
□ Scroll: To "Daftar Wajah" section
□ Click: "Buka Kamera" button
□ Allow: Camera permission in browser
□ Check: Video shows live camera feed
□ Wait: Green box + landmarks appear around face
□ Click: "Ambil Foto" button
□ Wait: "Wajah berhasil didaftarkan!" message
□ Click: "Tutup Kamera" button
□ Submit: Form with all required fields
□ Check: Response says success
□ Verify: Database - SELECT * FROM users WHERE id = last_id\G
   - face_enrolled should be: 1 (true)
   - face_data should have: JSON array with numbers

VERIFICATION TEST
□ Login: As the enrolled employee
□ Navigate: /employee/absence/masuk
□ Fill: Location (auto-fills from GPS)
□ Select: Status = "hadir"
□ Scroll: To "Face Recognition" section
□ Click: "Mulai Kamera" button
□ Allow: Camera permission
□ Check: Video shows live feed with canvas overlay
□ Wait: Green box around face
□ Click: "Ambil Foto" button
□ Wait: Photo captured message
□ Click: "Tutup Kamera" button
□ Submit: Form
□ Check: Response with verification status
□ Verify: Database - SELECT * FROM absences WHERE user_id = last_id\G
   - face_image should have: path like "faces/2026/02/04/..."
   - face_verified should be: 1 (true) or 0 (false)
□ Check: File exists - storage/app/public/faces/2026/02/04/

ERROR CHECKING
□ Browser console (F12): No JavaScript errors
□ Laravel logs (storage/logs/): Check for warnings
□ Network tab (F12): All models load successfully
```

---

## 🚨 COMMON ISSUES & FIXES

```
Issue: "Camera not starting"
└─ Solution:
   ├─ Check browser permissions (⚙️ → Privacy → Camera)
   ├─ Ensure using HTTPS or localhost
   ├─ Try different browser (Chrome recommended)
   └─ Restart browser

Issue: "Models loading forever"
└─ Solution:
   ├─ Check Network tab for 404 errors on model files
   ├─ Verify /public/models/ files exist
   ├─ Check file permissions: chmod 644 public/models/*
   ├─ Clear browser cache (Ctrl+Shift+Delete)
   └─ Check internet connection

Issue: "No face detected"
└─ Solution:
   ├─ Improve lighting (use lamp or natural light)
   ├─ Move face closer (30-60 cm from camera)
   ├─ Center face in frame
   ├─ Check camera resolution (Settings → Devices → Camera)
   └─ Try different angle

Issue: "Face data not saving to database"
└─ Solution:
   ├─ Check database connection in .env
   ├─ Run: php artisan migrate
   ├─ Check browser console for form submission errors
   ├─ Check face_descriptors input has value
   └─ Check storage/logs/ for Laravel errors

Issue: "Verification always fails"
└─ Solution:
   ├─ Verify user.face_enrolled = true in database
   ├─ Check user.face_data is not null/empty
   ├─ Re-enroll face with better lighting
   ├─ Check threshold (currently 0.6)
   └─ Note: Current implementation is simplified
```

---

## 📞 QUICK REFERENCE

| Need           | Location                                               | Method |
| -------------- | ------------------------------------------------------ | ------ |
| Initialize     | `FaceRecognitionHelper.initModels()`                   | Async  |
| Start camera   | `FaceRecognitionHelper.startCamera(video)`             | Async  |
| Stop camera    | `FaceRecognitionHelper.stopCamera(video)`              | Sync   |
| Live detection | `FaceRecognitionHelper.startDetection(v, c, cb)`       | Sync   |
| Stop detection | `FaceRecognitionHelper.stopDetection()`                | Sync   |
| Get descriptor | `FaceRecognitionHelper.getFaceDescriptors(input)`      | Async  |
| Capture image  | `FaceRecognitionHelper.captureFace(video)`             | Async  |
| Compare        | `FaceRecognitionHelper.compareFaceDescriptors(d1, d2)` | Sync   |
| Verify         | `FaceRecognitionHelper.verifyFace(c, e, t)`            | Sync   |
| Enroll         | `$user->enrollFace($descriptors)`                      | Sync   |
| Check enrolled | `$user->hasFaceEnrolled()`                             | Sync   |
| Get data       | `$user->getFaceData()`                                 | Sync   |

---

## 🎓 Key Concepts

```
TENSORFLOW.JS
└─ JavaScript runtime for ML models
   ├─ Runs in browser
   ├─ GPU accelerated (if available)
   └─ No server-side processing needed

FACE-API.JS
└─ Face detection & recognition library
   ├─ Built on TensorFlow.js
   ├─ Pre-trained models
   ├─ Multiple detection modes:
   │  ├─ SSD MobileNet v1 (fast)
   │  ├─ Tiny Face Detector (very fast)
   │  └─ MTCNN (accurate)
   └─ Returns: detection, landmarks, descriptor

FACE DESCRIPTOR
└─ 128-dimensional vector
   ├─ Unique per person
   ├─ Robust to pose/lighting variations
   ├─ Generated by FaceRecognitionNet
   └─ Compared using Euclidean distance

EUCLIDEAN DISTANCE
└─ sqrt(Σ(x_i - y_i)²)
   ├─ Distance between 2 points
   ├─ In this case: 2 face descriptors
   ├─ Lower = more similar
   ├─ Threshold typically 0.6
   └─ Used for: MATCH vs NO MATCH decision

VERIFICATION
└─ Comparing captured face with enrolled face
   ├─ Extract descriptor from current image
   ├─ Get enrolled descriptor from database
   ├─ Calculate Euclidean distance
   ├─ If distance < 0.6: MATCH ✓
   └─ If distance ≥ 0.6: NO MATCH ✗
```

---

**Version**: 1.0  
**Date**: February 4, 2026  
**For**: Rice Log Employee Attendance System  
**Status**: ✅ READY TO USE
