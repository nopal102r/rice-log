@extends('layouts.app')

@section('title', 'Presensi ' . ($type === 'masuk' ? 'Masuk' : 'Keluar'))

@section('extra-css')
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=places"></script>

    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script src="{{ asset('js/face-recognition-helper.js') }}"></script>
    <style>
        #videoContainer {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        video {
            width: 100%;
            border-radius: 12px;
            display: none;
        }

        #canvasContainer {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        canvas {
            position: absolute;
            top: 0;
            left: 0;
            display: none;
        }

        .distance-warning {
            background-color: #FEE2E2;
            border-left: 4px solid #DC2626;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-top: 1rem;
        }
    </style>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">
                Presensi {{ $type === 'masuk' ? 'Masuk' : 'Keluar' }}
            </h1>
            <p class="text-gray-600">Lengkapi data presensi Anda hari ini</p>
        </div>

        <!-- Map Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-map-marker-alt text-red-600"></i> Lokasi Anda
            </h2>
            <div id="map" style="width: 100%; height: 400px; border-radius: 12px; margin-bottom: 1rem;"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded">
                    <p class="text-gray-600 text-sm mb-2">Jarak dari Kantor</p>
                    <p class="text-3xl font-bold text-blue-600" id="distanceDisplay">-</p>
                </div>
                <div class="bg-gray-50 p-4 rounded">
                    <p class="text-gray-600 text-sm mb-2">Koordinat Anda</p>
                    <p class="text-sm text-gray-800" id="coordinatesDisplay">Mendapatkan lokasi...</p>
                </div>
            </div>

            <div id="distanceWarning" class="distance-warning" style="display: none;">
                <p class="text-red-700 font-bold mb-2">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Peringatan Jarak
                </p>
                <p class="text-red-600 text-sm">
                    Anda berada lebih dari 2 km dari kantor utama. Pastikan lokasi Anda tepat sebelum melanjutkan.
                </p>
            </div>
        </div>

        <!-- Absence Form -->
        <form id="absenceForm" class="bg-white rounded-lg shadow p-6">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="face_image" id="face_image">

            <!-- Status Selection (only for check-in) -->
            @if($type === 'masuk')
                <div class="mb-6">
                    <label class="block text-gray-800 font-bold mb-4">Pilih Status Presensi:</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="hadir" checked class="mr-2">
                            <div
                                class="border-2 border-gray-300 rounded-lg p-4 hover:border-blue-500 hover:bg-blue-50 transition">
                                <i class="fas fa-check-circle text-blue-600 text-2xl mb-2"></i>
                                <p class="font-bold text-gray-800">Hadir</p>
                                <p class="text-xs text-gray-600">Absen dengan face recognition</p>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="sakit" class="mr-2">
                            <div
                                class="border-2 border-gray-300 rounded-lg p-4 hover:border-red-500 hover:bg-red-50 transition">
                                <i class="fas fa-hospital-user text-red-600 text-2xl mb-2"></i>
                                <p class="font-bold text-gray-800">Sakit</p>
                                <p class="text-xs text-gray-600">Jelaskan kondisi Anda</p>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="izin" class="mr-2">
                            <div
                                class="border-2 border-gray-300 rounded-lg p-4 hover:border-yellow-500 hover:bg-yellow-50 transition">
                                <i class="fas fa-file-alt text-yellow-600 text-2xl mb-2"></i>
                                <p class="font-bold text-gray-800">Izin</p>
                                <p class="text-xs text-gray-600">Berikan alasan izin</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Description for sick/permission -->
                <div id="descriptionField" class="mb-6" style="display: none;">
                    <label class="block text-gray-800 font-bold mb-2">Keterangan:</label>
                    <textarea name="description" id="description" rows="4"
                        class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-500"
                        placeholder="Jelaskan alasan Anda..."></textarea>
                </div>
            @else
                <input type="hidden" name="status" value="hadir">
            @endif

            <!-- Face Recognition Section (for "Hadir" status) -->
            <div id="faceRecognitionSection" class="mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-camera text-purple-600"></i> Verifikasi Wajah
                </h3>

                <div class="bg-gray-50 rounded-lg p-6">
                    <p class="text-gray-600 mb-4">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Pastikan wajah Anda terlihat jelas di kamera. Sistem akan otomatis mendeteksi wajah Anda.
                    </p>

                    <div id="videoContainer" class="mb-4">
                        <video id="video" playsinline autoplay></video>
                        <div id="canvasContainer">
                            <canvas id="canvas"></canvas>
                        </div>
                    </div>

                    <div id="faceStatus" class="text-center mb-4">
                        <p class="text-gray-600">Memuat model face recognition...</p>
                    </div>

                    <div class="flex gap-2 justify-center mb-4">
                        <button type="button" id="startCameraBtn"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                            <i class="fas fa-video mr-2"></i> Mulai Kamera
                        </button>
                        <button type="button" id="captureBtn"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded"
                            style="display: none;">
                            <i class="fas fa-camera mr-2"></i> Ambil Foto
                        </button>
                        <button type="button" id="stopCameraBtn"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded"
                            style="display: none;">
                            <i class="fas fa-stop mr-2"></i> Hentikan Kamera
                        </button>
                    </div>

                    <div id="capturedFaceInfo" class="text-center text-green-600 font-bold" style="display: none;">
                        <i class="fas fa-check-circle mr-2"></i> Wajah berhasil ditangkap!
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4">
                <button type="submit"
                    class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 rounded-lg flex items-center justify-center gap-2">
                    <i class="fas fa-check"></i> Simpan Presensi
                </button>
                <a href="{{ route('employee.dashboard') }}"
                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 rounded-lg flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
@endsection

@section('extra-js')
    <script>
        const googleMapsApiKey = 'YOUR_GOOGLE_MAPS_API_KEY'; // Ganti dengan API key Anda
        const officeLocation = @json($officeLocation);
        const maxDistance = {{ $maxDistance }};

        let map, userMarker, officeMarker;
        let currentLat = 0, currentLon = 0;

        // Initialize Map
        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 15,
                center: { lat: officeLocation.latitude, lng: officeLocation.longitude }
            });

            // Office marker
            officeMarker = new google.maps.Marker({
                map: map,
                position: { lat: officeLocation.latitude, lng: officeLocation.longitude },
                title: 'Kantor Pusat',
                icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
            });

            // Get user location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    currentLat = position.coords.latitude;
                    currentLon = position.coords.longitude;

                    document.getElementById('latitude').value = currentLat;
                    document.getElementById('longitude').value = currentLon;
                    document.getElementById('coordinatesDisplay').textContent =
                        currentLat.toFixed(4) + ', ' + currentLon.toFixed(4);

                    // User marker
                    userMarker = new google.maps.Marker({
                        map: map,
                        position: { lat: currentLat, lng: currentLon },
                        title: 'Lokasi Anda',
                        icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
                    });

                    // Update center
                    map.setCenter(userMarker.getPosition());

                    // Calculate distance
                    calculateDistance();

                    // Draw line between user and office
                    const line = new google.maps.Polyline({
                        map: map,
                        path: [
                            { lat: currentLat, lng: currentLon },
                            { lat: officeLocation.latitude, lng: officeLocation.longitude }
                        ],
                        strokeColor: '#FF0000',
                        strokeOpacity: 0.5,
                        strokeWeight: 2
                    });
                });
            }
        }

        // Calculate distance using Haversine formula
        function calculateDistance() {
            const R = 6371; // Radius of earth in km
            const dLat = (officeLocation.latitude - currentLat) * Math.PI / 180;
            const dLon = (officeLocation.longitude - currentLon) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(currentLat * Math.PI / 180) * Math.cos(officeLocation.latitude * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const distance = R * c;

            document.getElementById('distanceDisplay').textContent = distance.toFixed(2) + ' km';

            if (distance > maxDistance) {
                document.getElementById('distanceWarning').style.display = 'block';
            } else {
                document.getElementById('distanceWarning').style.display = 'none';
            }
        }

        // Load face recognition models
        async function loadModels() {
            const MODEL_URL = window.location.origin + '/models/';
            const success = await FaceRecognitionHelper.initModels(MODEL_URL);

            if (success) {
                document.getElementById('faceStatus').innerHTML = '<p class="text-green-600 font-bold"><i class="fas fa-check-circle mr-2"></i> Siap untuk face recognition!</p>';
            } else {
                document.getElementById('faceStatus').innerHTML = '<p class="text-red-600">Gagal memuat model face recognition</p>';
            }
        }

        // Start camera
        document.getElementById('startCameraBtn').addEventListener('click', async function () {
            try {
                const video = document.getElementById('video');
                await FaceRecognitionHelper.startCamera(video);

                video.style.display = 'block';
                document.getElementById('startCameraBtn').style.display = 'none';
                document.getElementById('captureBtn').style.display = 'inline-block';
                document.getElementById('stopCameraBtn').style.display = 'inline-block';
                document.getElementById('faceStatus').innerHTML = '<p class="text-blue-600">Kamera aktif - Posisikan wajah Anda di depan kamera</p>';

                // Start face detection with callback
                FaceRecognitionHelper.startDetection(
                    video,
                    document.getElementById('canvas'),
                    function (faceDetected) {
                        if (faceDetected) {
                            document.getElementById('faceStatus').innerHTML = '<p class="text-green-600 font-bold"><i class="fas fa-check-circle mr-2"></i> Wajah terdeteksi! Silakan klik "Ambil Foto" untuk menyelesaikan</p>';
                        } else {
                            document.getElementById('faceStatus').innerHTML = '<p class="text-yellow-600">Mendeteksi wajah...</p>';
                        }
                    }
                );
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
            }
        });

        // Capture face
        document.getElementById('captureBtn').addEventListener('click', async function () {
            try {
                const video = document.getElementById('video');
                const blob = await FaceRecognitionHelper.captureFace(video);

                const file = new File([blob], 'face.jpg', { type: 'image/jpeg' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                document.getElementById('face_image').files = dataTransfer.files;

                document.getElementById('capturedFaceInfo').style.display = 'block';
                document.getElementById('captureBtn').style.display = 'none';
                document.getElementById('stopCameraBtn').click();
            } catch (error) {
                Swal.fire('Error', 'Gagal mengambil foto: ' + error.message, 'error');
            }
        });

        // Stop camera
        document.getElementById('stopCameraBtn').addEventListener('click', function () {
            FaceRecognitionHelper.stopDetection();
            FaceRecognitionHelper.stopCamera();

            document.getElementById('video').style.display = 'none';
            document.getElementById('canvas').style.display = 'none';
            document.getElementById('startCameraBtn').style.display = 'inline-block';
            document.getElementById('captureBtn').style.display = 'none';
            document.getElementById('stopCameraBtn').style.display = 'none';
            document.getElementById('faceStatus').innerHTML = '<p class="text-gray-600">Kamera dihentikan</p>';
        });

        // Handle status change
        document.querySelectorAll('input[name="status"]').forEach(radio => {
            radio.addEventListener('change', function () {
                const descField = document.getElementById('descriptionField');
                const faceSection = document.getElementById('faceRecognitionSection');

                if (this.value === 'hadir') {
                    descField.style.display = 'none';
                    faceSection.style.display = 'block';
                } else {
                    descField.style.display = 'block';
                    faceSection.style.display = 'none';
                }
            });
        });

        // Handle form submission
        document.getElementById('absenceForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const status = document.querySelector('input[name="status"]:checked').value;
            const description = document.getElementById('description')?.value;
            const faceImage = document.getElementById('face_image').files;

            if (status === 'hadir' && faceImage.length === 0) {
                Swal.fire('Error', 'Silakan ambil foto wajah terlebih dahulu', 'error');
                return;
            }

            if ((status === 'sakit' || status === 'izin') && !description) {
                Swal.fire('Error', 'Silakan isi keterangan', 'error');
                return;
            }

            const formData = new FormData(this);

            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch('{{ route("employee.absence.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire('Berhasil!', data.message, 'success').then(() => {
                        window.location.href = '{{ route("employee.dashboard") }}';
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'Terjadi kesalahan: ' + error.message, 'error');
            }
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            loadModels();
            initMap();
        });
    </script>
@endsection