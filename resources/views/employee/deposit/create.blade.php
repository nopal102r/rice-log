@extends('layouts.app')

@section('title', 'Setor Beras')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Setor Beras</h1>
            <p class="text-gray-600">Setor beras Anda dan dapatkan penghasilan sesuai dengan bobot.</p>
        </div>

        <div class="bg-white rounded-lg shadow p-8">
            <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-6">
                <p class="text-blue-800 text-sm">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Catatan:</strong> Anda harus melakukan absen masuk terlebih dahulu sebelum dapat melakukan setor
                    beras.
                </p>
            </div>

            <form id="depositForm">
                @csrf

                <div class="mb-6">
                    <label class="block text-gray-800 font-bold mb-2">
                        <i class="fas fa-weight text-green-600 mr-2"></i> Berat Beras (kg)
                    </label>
                    <input type="number" name="weight" id="weight" required step="0.1" min="0.1"
                        class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-green-500"
                        placeholder="Masukkan berat beras dalam kg">
                    <p class="text-xs text-gray-500 mt-1">Contoh: 25.5 kg</p>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-800 font-bold mb-2">
                        <i class="fas fa-images text-green-600 mr-2"></i> Foto Beras
                    </label>
                    <div class="relative border-2 border-dashed border-green-300 rounded-lg p-6 cursor-pointer hover:bg-green-50 transition"
                        id="photoDropZone">
                        <input type="file" name="photo" id="photo" required accept="image/*" class="hidden">
                        <div class="text-center">
                            <i class="fas fa-camera text-4xl text-green-400 mb-2"></i>
                            <p class="text-gray-700 font-bold">Klik atau seret gambar ke sini</p>
                            <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG (Max 5MB)</p>
                        </div>
                    </div>
                    <div id="photoPreview" class="mt-4" style="display: none;">
                        <img id="photoImg" src="" alt="Preview" class="w-full max-w-xs rounded">
                        <button type="button" id="removePhotoBtn"
                            class="mt-2 text-red-600 hover:text-red-800 text-sm font-bold">
                            <i class="fas fa-trash mr-1"></i> Hapus Foto
                        </button>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-800 font-bold mb-2">
                        <i class="fas fa-sticky-note text-green-600 mr-2"></i> Catatan (Opsional)
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                        class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-green-500"
                        placeholder="Tambahkan catatan atau informasi tambahan..."></textarea>
                </div>

                <div class="bg-green-50 border border-green-200 rounded p-4 mb-6">
                    <p class="text-green-800 font-bold mb-2">Perhitungan Upah:</p>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Harga per kg: <span class="font-bold">Rp 30.000</span></span>
                        <span class="text-gray-700">Berat: <span id="weightDisplay" class="font-bold">0</span> kg</span>
                    </div>
                    <div class="border-t border-green-200 mt-3 pt-3 text-xl font-bold text-green-700">
                        Total: Rp <span id="totalPrice">0</span>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-3 rounded-lg flex items-center justify-center gap-2">
                        <i class="fas fa-check"></i> Setor Beras
                    </button>
                    <a href="{{ route('employee.dashboard') }}"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 rounded-lg flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        const pricePerKg = 30000;
        const weightInput = document.getElementById('weight');
        const photoInput = document.getElementById('photo');
        const photoDropZone = document.getElementById('photoDropZone');
        const photoPreview = document.getElementById('photoPreview');
        const photoImg = document.getElementById('photoImg');
        const removePhotoBtn = document.getElementById('removePhotoBtn');

        // Update price calculation
        weightInput.addEventListener('change', function () {
            const weight = parseFloat(this.value) || 0;
            const total = weight * pricePerKg;
            document.getElementById('weightDisplay').textContent = weight.toFixed(1);
            document.getElementById('totalPrice').textContent = total.toLocaleString('id-ID');
        });

        // Drag and drop
        photoDropZone.addEventListener('click', () => photoInput.click());
        photoDropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            photoDropZone.classList.add('bg-green-100');
        });
        photoDropZone.addEventListener('dragleave', () => {
            photoDropZone.classList.remove('bg-green-100');
        });
        photoDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            photoDropZone.classList.remove('bg-green-100');
            if (e.dataTransfer.files.length) {
                photoInput.files = e.dataTransfer.files;
                handlePhotoSelect();
            }
        });

        photoInput.addEventListener('change', handlePhotoSelect);

        function handlePhotoSelect() {
            if (photoInput.files && photoInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    photoImg.src = e.target.result;
                    photoPreview.style.display = 'block';
                };
                reader.readAsDataURL(photoInput.files[0]);
            }
        }

        removePhotoBtn.addEventListener('click', function () {
            photoInput.value = '';
            photoPreview.style.display = 'none';
        });

        document.getElementById('depositForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const weight = parseFloat(weightInput.value);
            if (!weight || weight <= 0) {
                Swal.fire('Error', 'Silakan masukkan berat beras yang valid', 'error');
                return;
            }

            if (!photoInput.files || photoInput.files.length === 0) {
                Swal.fire('Error', 'Silakan unggah foto beras', 'error');
                return;
            }

            const formData = new FormData(this);

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Setor ' + weight + ' kg beras? Total: Rp ' + (weight * pricePerKg).toLocaleString('id-ID'),
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setor!',
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
                        const response = await fetch('{{ route("employee.deposit.store") }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            Swal.fire('Berhasil!', data.message, 'success').then(() => {
                                window.location.href = '{{ route("employee.deposit.my-deposits") }}';
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
    </script>
@endsection