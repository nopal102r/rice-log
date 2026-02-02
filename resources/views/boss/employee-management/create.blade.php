@extends('layouts.app')

@section('title', 'Tambah Karyawan Baru')

@section('extra-css')
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4"></script>
    <script src="https://cdn.jsdelivr.net/gh/vladmandic/face-api@0.8.5/dist/face-api.min.js"></script>
    <script src="{{ asset('js/face-recognition-helper.js') }}"></script>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Tambah Karyawan Baru</h1>
            <p class="text-gray-600">Daftarkan karyawan baru ke sistem</p>
        </div>

        <div class="bg-white rounded-lg shadow p-8">
            <form id="addEmployeeForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-2">
                            <i class="fas fa-user text-blue-600 mr-2"></i> Nama Lengkap
                        </label>
                        <input type="text" name="name" id="name" required
                            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-500"
                            placeholder="Nama karyawan">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-2">
                            <i class="fas fa-envelope text-blue-600 mr-2"></i> Email
                        </label>
                        <input type="email" name="email" id="email" required
                            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-500"
                            placeholder="example@mail.com">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-2">
                            <i class="fas fa-phone text-blue-600 mr-2"></i> Nomor Telepon
                        </label>
                        <input type="tel" name="phone" id="phone" required
                            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-500"
                            placeholder="081234567890">
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-2">
                            <i class="fas fa-birthday-cake text-blue-600 mr-2"></i> Tanggal Lahir
                        </label>
                        <input type="date" name="date_of_birth" id="date_of_birth" required
                            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-500">
                    </div>

                    <!-- Job -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-2">
                            <i class="fas fa-briefcase text-blue-600 mr-2"></i> Posisi/Pekerjaan
                        </label>
                        <select name="job" id="job" required
                            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-500">
                            <option value="">-- Pilih Posisi --</option>
                            <option value="kurir">Kurir</option>
                            <option value="sawah">Sawah (Petani)</option>
                            <option value="ngegiling">Ngegiling (Operator Mesin)</option>
                        </select>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-2">
                            <i class="fas fa-lock text-blue-600 mr-2"></i> Password
                        </label>
                        <input type="password" name="password" id="password" required minlength="8"
                            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-500"
                            placeholder="Minimal 8 karakter">
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-2">
                            <i class="fas fa-lock text-blue-600 mr-2"></i> Konfirmasi Password
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            minlength="8"
                            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-500"
                            placeholder="Konfirmasi password">
                    </div>
                </div>

                <!-- Address -->
                <div class="mt-6">
                    <label class="block text-gray-800 font-bold mb-2">
                        <i class="fas fa-home text-blue-600 mr-2"></i> Alamat (Opsional)
                    </label>
                    <textarea name="address" id="address" rows="3"
                        class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-500"
                        placeholder="Alamat rumah karyawan"></textarea>
                </div>

                <!-- Face Enrollment Section -->
                <div class="mt-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="fas fa-face-smile text-purple-600 mr-2"></i> Daftar Wajah (Opsional)
                    </h3>

                    <div id="videoContainer" style="display: none;" class="mb-4">
                        <div class="relative w-full max-w-md mx-auto"
                            style="position: relative; max-width: 500px; margin: 0 auto;">
                            <video id="enrollmentVideo" autoplay muted playsinline
                                style="width: 100%; border: 2px solid #9333ea; border-radius: 12px; display: block;"></video>
                            <canvas id="enrollmentCanvas" style="position: absolute; top: 0; left: 0; border-radius: 12px;"
                                display="block"></canvas>
                        </div>
                    </div>

                    <div id="faceEnrollmentStatus" class="text-center mb-4">
                        <p class="text-gray-600">Siap untuk daftar wajah</p>
                    </div>

                    <div class="flex gap-2 justify-center mb-4">
                        <button type="button" id="startEnrollmentCameraBtn"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded">
                            <i class="fas fa-video mr-2"></i> Buka Kamera
                        </button>
                        <button type="button" id="captureEnrollmentBtn"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded"
                            style="display: none;">
                            <i class="fas fa-camera mr-2"></i> Ambil Foto
                        </button>
                        <button type="button" id="stopEnrollmentCameraBtn"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded"
                            style="display: none;">
                            <i class="fas fa-stop mr-2"></i> Tutup Kamera
                        </button>
                    </div>

                    <div id="faceEnrollmentSuccess" class="text-center text-green-600 font-bold" style="display: none;">
                        <i class="fas fa-check-circle mr-2"></i> Wajah berhasil didaftarkan!
                    </div>

                    <input type="hidden" name="face_descriptors" id="face_descriptors">
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded p-3 text-sm text-yellow-800 mt-4">
                    <i class="fas fa-warning mr-2"></i>
                    <strong>Penting:</strong> Pastikan pencahayaan bagus dan wajah terlihat jelas. Anda bisa skip
                    langkah ini untuk sekarang.
                </div>

                <!-- Submit Button -->
                <div class="mt-8">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition duration-200">
                        <i class="fas fa-check mr-2"></i> Daftarkan Karyawan
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('boss.employees.index') }}"
                class="inline-block text-blue-600 hover:text-blue-800 font-semibold">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Karyawan
            </a>
        </div>
    </div>
@endsection

@section('css')
    <style>
        #enrollmentCanvas {
            position: absolute;
            top: 0;
            left: 0;
            border-radius: 12px;
        }

        /* Ensure buttons are always clickable */
        #captureEnrollmentBtn {
            position: relative;
            z-index: 10;
            cursor: pointer;
        }

        #captureEnrollmentBtn:hover {
            opacity: 0.9;
        }
    </style>
@endsection

@section('extra-js')
    <script>
        let faceDescriptorsData = null;

        // Wait for tfjs and face-api to be available
        function waitForFaceApi(callback, attempts = 50) {
            if (typeof tf !== 'undefined' && typeof faceapi !== 'undefined') {
                console.log('✓ tfjs and faceapi are loaded');
                callback();
            } else if (attempts > 0) {
                console.log('Waiting for tfjs/faceapi... attempts left:', attempts);
                setTimeout(() => waitForFaceApi(callback, attempts - 1), 100);
            } else {
                console.error('❌ tfjs or faceapi failed to load after 5 seconds');
                document.getElementById('faceEnrollmentStatus').innerHTML = '<p class="text-red-600 font-bold"><i class="fas fa-exclamation-circle mr-2"></i> TensorFlow.js or face-api failed to load</p>';
            }
        }

        // Wrap everything in DOMContentLoaded to ensure all elements exist
        document.addEventListener('DOMContentLoaded', function () {
            console.log('DOM Content Loaded - initializing face enrollment...');

            // Wait for libraries, then init
            waitForFaceApi(async function () {
                // Load models for face enrollment
                async function initFaceEnrollment() {
                    const MODEL_URL = window.location.origin + '/models/';
                    console.log('Initializing face enrollment with model path:', MODEL_URL);
                    const success = await FaceRecognitionHelper.initModels(MODEL_URL);

                    if (success) {
                        console.log('Face models loaded successfully');
                        document.getElementById('faceEnrollmentStatus').innerHTML = '<p class="text-green-600 font-bold"><i class="fas fa-check-circle mr-2"></i> Siap untuk daftar wajah</p>';
                    } else {
                        console.error('Failed to load face models');
                        document.getElementById('faceEnrollmentStatus').innerHTML = '<p class="text-red-600 font-bold"><i class="fas fa-exclamation-circle mr-2"></i> Error loading face models - buka console (F12) untuk detail</p>';
                    }
                }

                // Initialize face enrollment on page load
                initFaceEnrollment();

                // Start enrollment camera
                const startBtn = document.getElementById('startEnrollmentCameraBtn');
                if (startBtn) {
                    console.log('✓ Start button found');
                    startBtn.addEventListener('click', async function () {
                        try {
                            console.log('Starting enrollment camera...');
                            const video = document.getElementById('enrollmentVideo');
                            const canvas = document.getElementById('enrollmentCanvas');

                            console.log('Video element:', video);
                            console.log('Canvas element:', canvas);

                            await FaceRecognitionHelper.startCamera(video);
                            console.log('Camera started, stream ready');

                            document.getElementById('videoContainer').style.display = 'block';
                            document.getElementById('startEnrollmentCameraBtn').style.display = 'none';
                            document.getElementById('captureEnrollmentBtn').style.display = 'inline-block';
                            document.getElementById('stopEnrollmentCameraBtn').style.display = 'inline-block';
                            document.getElementById('faceEnrollmentStatus').innerHTML = '<p class="text-blue-600">Posisikan wajah Anda di depan kamera...</p>';

                            // Wait a bit for video to load
                            setTimeout(() => {
                                console.log('Starting face detection...');
                                // Start real-time face detection
                                FaceRecognitionHelper.startDetection(video, canvas, function (faceDetected) {
                                    console.log('Face detected:', faceDetected);
                                    if (faceDetected) {
                                        document.getElementById('faceEnrollmentStatus').innerHTML = '<p class="text-green-600 font-bold"><i class="fas fa-check-circle mr-2"></i> Wajah terdeteksi! Klik "Ambil Foto"</p>';
                                    } else {
                                        document.getElementById('faceEnrollmentStatus').innerHTML = '<p class="text-yellow-600">Mendeteksi wajah...</p>';
                                    }
                                });
                            }, 500);
                        } catch (error) {
                            console.error('Error starting camera:', error);
                            Swal.fire('Error', error.message, 'error');
                        }
                    });
                }

                // Capture face for enrollment
                const captureBtn = document.getElementById('captureEnrollmentBtn');
                if (captureBtn) {
                    console.log('✓ Capture button found');
                    captureBtn.addEventListener('click', async function () {
                        try {
                            console.log('Capture button clicked');
                            const video = document.getElementById('enrollmentVideo');

                            console.log('Getting face descriptors from video...');
                            // Get face descriptors
                            const descriptors = await FaceRecognitionHelper.getFaceDescriptors(video);
                            console.log('Descriptors found:', descriptors.length);

                            if (descriptors.length === 0) {
                                console.warn('No face detected - but capturing anyway for manual verification');
                            }

                            // Store descriptors if found
                            if (descriptors.length > 0) {
                                faceDescriptorsData = descriptors[0];
                                document.getElementById('face_descriptors').value = JSON.stringify(descriptors[0]);
                                console.log('Face descriptors stored');
                            } else {
                                console.log('No descriptors - will proceed without face data');
                                document.getElementById('face_descriptors').value = '';
                            }

                            document.getElementById('faceEnrollmentSuccess').style.display = 'block';
                            document.getElementById('captureEnrollmentBtn').style.display = 'none';

                            // Small delay before stopping camera
                            setTimeout(() => {
                                document.getElementById('stopEnrollmentCameraBtn').click();
                            }, 500);

                            Swal.fire('Info', 'Foto berhasil diambil! Anda bisa lanjut submit form.', 'success');
                        } catch (error) {
                            console.error('Capture error:', error);
                            Swal.fire('Error', 'Gagal mengambil foto: ' + error.message, 'error');
                        }
                    });
                }

                // Stop enrollment camera
                const stopBtn = document.getElementById('stopEnrollmentCameraBtn');
                if (stopBtn) {
                    console.log('✓ Stop button found');
                    stopBtn.addEventListener('click', function () {
                        FaceRecognitionHelper.stopDetection();
                        FaceRecognitionHelper.stopCamera();

                        document.getElementById('videoContainer').style.display = 'none';
                        document.getElementById('startEnrollmentCameraBtn').style.display = 'inline-block';
                        document.getElementById('captureEnrollmentBtn').style.display = 'none';
                        document.getElementById('stopEnrollmentCameraBtn').style.display = 'none';
                    });
                }

                // Form submission
                const addEmployeeForm = document.getElementById('addEmployeeForm');
                if (addEmployeeForm) {
                    console.log('✓ Employee form found');
                    addEmployeeForm.addEventListener('submit', async function (e) {
                        e.preventDefault();

                        const name = document.getElementById('name').value;
                        const email = document.getElementById('email').value;
                        const phone = document.getElementById('phone').value;
                        const dateOfBirth = document.getElementById('date_of_birth').value;
                        const job = document.getElementById('job').value;
                        const password = document.getElementById('password').value;
                        const passwordConfirmation = document.getElementById('password_confirmation').value;
                        const address = document.getElementById('address').value;
                        const faceDescriptors = document.getElementById('face_descriptors').value;

                        if (password !== passwordConfirmation) {
                            Swal.fire('Error', 'Password tidak cocok!', 'error');
                            return;
                        }

                        Swal.fire({
                            title: 'Konfirmasi',
                            text: 'Tambahkan ' + name + ' sebagai karyawan baru?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Tambah!',
                            cancelButtonText: 'Batal'
                        }).then(async (result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: 'Memproses...',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                try {
                                    const response = await fetch('{{ route("boss.employees.store") }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                                        },
                                        body: JSON.stringify({
                                            name: name,
                                            email: email,
                                            phone: phone,
                                            date_of_birth: dateOfBirth,
                                            job: job,
                                            password: password,
                                            password_confirmation: passwordConfirmation,
                                            address: address,
                                            face_descriptors: faceDescriptors ? JSON.parse(faceDescriptors) : null
                                        })
                                    });

                                    const data = await response.json();

                                    if (data.success) {
                                        Swal.fire('Berhasil!', data.message, 'success').then(() => {
                                            window.location.href = '{{ route("boss.employees.index") }}';
                                        });
                                    } else {
                                        Swal.fire('Error', data.message, 'error');
                                    }
                                } catch (error) {
                                    Swal.fire('Error', 'Terjadi kesalahan: ' + error.message, 'error');
                                }
                            }
                        });
                    });
                }
            });  // End waitForFaceApi callback`r`n        }); // End DOMContentLoaded
    </script>
@endsection
