@extends('layouts.app')

@section('title', 'Laporan Kerja')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Laporan Kerja</h1>
            <p class="text-gray-600">
                Halo, {{ Auth::user()->name }} ({{ ucfirst(Auth::user()->job) }}). Silakan lapor kerjaan Anda.
            </p>
        </div>

        <div class="bg-white rounded-lg shadow p-8">

            <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-6">
                <p class="text-blue-800 text-sm">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Catatan:</strong> Pastikan data yang Anda masukkan sesuai dengan kenyataan.
                </p>
            </div>

            <form id="depositForm">
                @csrf
                
                {{-- Dynamic Fields based on Role --}}
                
                @if($user->isFarmer())
                    {{-- PETANI: Toggle Type --}}
                    <div class="mb-6">
                        <label class="block text-gray-800 font-bold mb-2">Jenis Kegiatan</label>
                        <div class="flex gap-4">
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="type" value="land_management" class="peer hidden" checked>
                                <div class="p-4 border border-gray-300 rounded-lg text-center bg-gray-50 peer-checked:bg-green-100 peer-checked:border-green-500 peer-checked:text-green-800">
                                    <i class="fas fa-tractor mb-2 text-2xl"></i><br>Urus Lahan
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" name="type" value="regular" class="peer hidden">
                                <div class="p-4 border border-gray-300 rounded-lg text-center bg-gray-50 peer-checked:bg-green-100 peer-checked:border-green-500 peer-checked:text-green-800">
                                    <i class="fas fa-seedling mb-2 text-2xl"></i><br>Setor Beras/Pare
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Form: Urus Lahan --}}
                    <div id="landManagementForm">
                        <div class="mb-4">
                            <label class="block text-gray-800 font-bold mb-2">Jumlah Kotak Sawah</label>
                            <input type="number" name="box_count" class="w-full border border-gray-300 rounded px-4 py-2" min="1">
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-800 font-bold mb-2">Jam Mulai</label>
                                <input type="time" name="start_time" class="w-full border border-gray-300 rounded px-4 py-2">
                            </div>
                            <div>
                                <label class="block text-gray-800 font-bold mb-2">Jam Selesai</label>
                                <input type="time" name="end_time" class="w-full border border-gray-300 rounded px-4 py-2">
                            </div>
                        </div>
                    </div>

                    {{-- Form: Setor Beras (Hidden by default) --}}
                    <div id="regularForm" class="hidden">
                        <div class="mb-4">
                            <label class="block text-gray-800 font-bold mb-2">Berat (Kg)</label>
                            <input type="number" name="weight" id="weight_farmer" class="w-full border border-gray-300 rounded px-4 py-2" step="0.1" min="0.1">
                        </div>
                    </div>
                
                @elseif($user->isSales())
                    {{-- SALES --}}
                    <div class="mb-4">
                        <label class="block text-gray-800 font-bold mb-2">
                            <i class="fas fa-box text-purple-600 mr-2"></i> Ukuran Karung
                        </label>
                        <select name="sack_size" id="sack_size" class="w-full border border-gray-300 rounded px-4 py-2" required>
                            <option value="">-- Pilih Ukuran --</option>
                            <option value="25">25 kg/karung</option>
                            <option value="15">15 kg/karung</option>
                            <option value="10">10 kg/karung</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-800 font-bold mb-2">
                            <i class="fas fa-boxes text-purple-600 mr-2"></i> Jumlah Karung Terjual
                        </label>
                        <input type="number" name="sack_count" id="sack_count" class="w-full border border-gray-300 rounded px-4 py-2" required min="1" step="1" placeholder="Contoh: 10">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-800 font-bold mb-2">Uang Setoran (Rp)</label>
                        <input type="number" name="money_amount" id="money_amount" class="w-full border border-gray-300 rounded px-4 py-2" required placeholder="Contoh: 5000000">
                    </div>
                    {{-- Hidden field for calculated weight --}}
                    <input type="hidden" name="weight" id="weight_sales">
                    <div class="bg-blue-50 border border-blue-200 rounded p-3 mb-4">
                        <p class="text-sm text-gray-700">
                            <i class="fas fa-calculator text-blue-600 mr-2"></i>
                            <strong>Total Berat:</strong> <span id="total_weight_display">0</span> kg
                        </p>
                    </div>

                @elseif($user->isPacking())
                    {{-- PACKING: Multi-item Sack Sizes --}}
                    <div class="mb-6">
                        <label class="block text-gray-800 font-bold mb-4 flex justify-between items-center">
                            <span><i class="fas fa-boxes-stacked text-blue-600 mr-2"></i> Hasil Packing (Beras Karung)</span>
                            <button type="button" id="addPackItemBtn" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-1 px-3 rounded-full transition">
                                <i class="fas fa-plus mr-1"></i> Tambah Baris
                            </button>
                        </label>
                        
                        <div id="packingFormItems" class="space-y-3">
                            <div class="packing-item grid grid-cols-5 gap-2 mb-2 items-end">
                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500 mb-1 font-bold">Ukuran Karung</label>
                                    <select name="details[0][size]" class="pack-size w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-blue-500 outline-none">
                                        <option value="25">25 kg</option>
                                        <option value="20">20 kg</option>
                                        <option value="15">15 kg</option>
                                        <option value="10">10 kg</option>
                                        <option value="5">5 kg</option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500 mb-1 font-bold">Jumlah Karung</label>
                                    <input type="number" name="details[0][count]" class="pack-count w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-blue-500 outline-none" min="1" value="1">
                                </div>
                                <div class="col-span-1">
                                    <button type="button" class="remove-pack-item bg-red-100 text-red-600 rounded p-2 hover:bg-red-200 transition h-[40px] w-full">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-3 rounded border border-gray-200">
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Karung</p>
                                <p class="text-xl font-black text-gray-800" id="packing_total_sacks">1</p>
                            </div>
                            <div class="bg-gray-50 p-3 rounded border border-gray-200">
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Berat (Kg)</p>
                                <p class="text-xl font-black text-gray-800"><span id="packing_total_weight">25</span> kg</p>
                            </div>
                        </div>
                    </div>

                @else
                    {{-- SUPIR & NGEGILING & OTHERS --}}
                    <div class="mb-4">
                        <label class="block text-gray-800 font-bold mb-2">Berat (Kg)</label>
                        <input type="number" name="weight" id="weight_general" class="w-full border border-gray-300 rounded px-4 py-2" required step="0.1" min="0.1">
                    </div>
                @endif


                {{-- Common Fields --}}
                <div class="mb-6 mt-6">
                    <label class="block text-gray-800 font-bold mb-2">
                        <i class="fas fa-camera text-green-600 mr-2"></i> Bukti Foto
                    </label>
                    <div class="relative border-2 border-dashed border-green-300 rounded-lg p-6 cursor-pointer hover:bg-green-50 transition"
                        id="photoDropZone">
                        <input type="file" name="photo" id="photo" required accept="image/*" class="hidden">
                        <div class="text-center">
                            <i class="fas fa-camera text-4xl text-green-400 mb-2"></i>
                            <p class="text-gray-700 font-bold">Klik atau seret foto ke sini</p>
                        </div>
                    </div>
                    <div id="photoPreview" class="mt-4 hidden">
                        <img id="photoImg" src="" alt="Preview" class="w-full max-w-xs rounded">
                        <button type="button" id="removePhotoBtn" class="mt-2 text-red-600 font-bold text-sm">Hapus Foto</button>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-800 font-bold mb-2">Catatan (Opsional)</label>
                    <textarea name="notes" id="notes" rows="2" class="w-full border border-gray-300 rounded px-4 py-2"></textarea>
                </div>

                {{-- Wage Estimation --}}
                <div class="bg-green-50 border border-green-200 rounded p-4 mb-6">
                    <p class="text-green-800 font-bold mb-2">Estimasi Pendapatan:</p>
                    <div class="text-2xl font-bold text-green-700">
                        Rp <span id="wageDisplay">0</span>
                    </div>
                    <p id="wageFormula" class="text-xs text-green-600 mt-1"></p>
                </div>

                <div class="flex gap-4">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-3 rounded-lg flex items-center justify-center gap-2">
                        <i class="fas fa-check"></i> Kirim Laporan
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
        // Settings from Server
        const settings = @json($settings);
        const userJob = "{{ $user->job }}";
        
        // Elements
        const form = document.getElementById('depositForm');
        const wageDisplay = document.getElementById('wageDisplay');
        const wageFormula = document.getElementById('wageFormula');

        // Logic for Farmer Toggle
        const landForm = document.getElementById('landManagementForm');
        const regularForm = document.getElementById('regularForm');
        const radioType = document.querySelectorAll('input[name="type"]');
        
        if (userJob === 'petani') {
            radioType.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'land_management') {
                        landForm.classList.remove('hidden');
                        regularForm.classList.add('hidden');
                    } else {
                        landForm.classList.add('hidden');
                        regularForm.classList.remove('hidden');
                    }
                    calculateWage();
                });
            });
        }

        // Calculations
        function calculateWage() {
            let wage = 0;
            let formula = '';

            if (userJob === 'supir') {
                const w = parseFloat(document.getElementById('weight_general').value) || 0;
                wage = w * settings.driver_rate_per_kg;
                formula = `${w} kg x Rp ${settings.driver_rate_per_kg}`;
            
            } else if (userJob === 'ngegiling') {
                const w = parseFloat(document.getElementById('weight_general').value) || 0;
                wage = w * settings.miller_rate_per_kg;
                formula = `${w} kg x Rp ${settings.miller_rate_per_kg}`;
            
            } else if (userJob === 'petani') {
                const type = document.querySelector('input[name="type"]:checked').value;
                if (type === 'land_management') {
                    const boxes = parseInt(document.querySelector('input[name="box_count"]').value) || 0;
                    wage = boxes * settings.farmer_rate_per_box;
                    formula = `${boxes} kotak x Rp ${settings.farmer_rate_per_box}`;
                } else {
                    const w = parseFloat(document.getElementById('weight_farmer').value) || 0;
                    wage = w * settings.price_per_kg; // Using Rice Price
                    formula = `${w} kg x Rp ${settings.price_per_kg}`;
                }

            } else if (userJob === 'sales') {
                const sackSize = parseFloat(document.getElementById('sack_size').value) || 0;
                const sackCount = parseInt(document.getElementById('sack_count').value) || 0;
                const money = parseFloat(document.getElementById('money_amount').value) || 0;
                
                const weight = sackSize * sackCount;
                document.getElementById('weight_sales').value = weight;
                
                const weightDisplay = document.getElementById('total_weight_display');
                if (weightDisplay) weightDisplay.textContent = weight.toFixed(1);
                
                if (sackCount > 0) {
                    wage = money / sackCount;
                    formula = `Rp ${money.toLocaleString('id-ID')} / ${sackCount} karung = Rp ${wage.toLocaleString('id-ID')}/karung`;
                }

            } else if (userJob === 'packing') {
                const items = document.querySelectorAll('.packing-item');
                let totalWeight = 0;
                let totalSacks = 0;

                items.forEach(item => {
                    const size = parseFloat(item.querySelector('.pack-size').value) || 0;
                    const count = parseInt(item.querySelector('.pack-count').value) || 0;
                    totalWeight += size * count;
                    totalSacks += count;
                });

                wage = totalWeight * settings.packing_rate_per_kg;
                formula = `${totalWeight.toFixed(1)} kg x Rp ${settings.packing_rate_per_kg}/kg`;

                document.getElementById('packing_total_weight').textContent = totalWeight.toFixed(1);
                document.getElementById('packing_total_sacks').textContent = totalSacks;
            }

            wageDisplay.textContent = wage.toLocaleString('id-ID');
            wageFormula.textContent = formula;
        }

        // --- PACKING ITEMS DYNAMIC LOGIC ---
        if (userJob === 'packing') {
            const packingContainer = document.getElementById('packingFormItems');
            const addBtn = document.getElementById('addPackItemBtn');
            let itemIndex = 1;

            addBtn.addEventListener('click', () => {
                const div = document.createElement('div');
                div.className = 'packing-item grid grid-cols-5 gap-2 mb-2 items-end';
                div.innerHTML = `
                    <div class="col-span-2">
                        <label class="block text-xs text-gray-500 mb-1 font-bold">Ukuran Karung</label>
                        <select name="details[${itemIndex}][size]" class="pack-size w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-blue-500 outline-none">
                            <option value="25">25 kg</option>
                            <option value="20">20 kg</option>
                            <option value="15">15 kg</option>
                            <option value="10">10 kg</option>
                            <option value="5">5 kg</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs text-gray-500 mb-1 font-bold">Jumlah Karung</label>
                        <input type="number" name="details[${itemIndex}][count]" class="pack-count w-full border border-gray-300 rounded px-3 py-2 text-sm focus:border-blue-500 outline-none" min="1" value="1">
                    </div>
                    <div class="col-span-1">
                        <button type="button" class="remove-pack-item bg-red-100 text-red-600 rounded p-2 hover:bg-red-200 transition h-[40px] w-full">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                packingContainer.appendChild(div);
                itemIndex++;
                attachDynamicListeners();
                calculateWage();
            });

            function attachDynamicListeners() {
                document.querySelectorAll('.pack-size, .pack-count').forEach(el => {
                    el.removeEventListener('input', calculateWage);
                    el.addEventListener('input', calculateWage);
                });
                document.querySelectorAll('.remove-pack-item').forEach(btn => {
                    btn.onclick = function() {
                        const items = document.querySelectorAll('.packing-item');
                        if (items.length > 1) {
                            this.closest('.packing-item').remove();
                            calculateWage();
                        } else {
                            Swal.fire('Info', 'Minimal harus ada 1 item.', 'info');
                        }
                    };
                });
            }
            attachDynamicListeners();
        }

        // Driver sack calculation
        if (userJob === 'supir') {
            const sackSize = document.getElementById('driver_sack_size');
            const sackCount = document.getElementById('driver_sack_count');
            const weightInput = document.getElementById('weight_general');

            const updateDriverWeight = () => {
                const size = parseFloat(sackSize.value) || 0;
                const count = parseInt(sackCount.value) || 0;
                if (size > 0 && count > 0) {
                    weightInput.value = (size * count).toFixed(1);
                }
                calculateWage();
            };

            sackSize.addEventListener('change', updateDriverWeight);
            sackCount.addEventListener('input', updateDriverWeight);
        }

        // Attach listeners to all inputs and selects
        document.querySelectorAll('input, select').forEach(element => {
            element.addEventListener('input', calculateWage);
            element.addEventListener('change', calculateWage);
        });

        // Photo Upload Logic (Existing)
        const photoInput = document.getElementById('photo');
        const photoDropZone = document.getElementById('photoDropZone');
        const photoPreview = document.getElementById('photoPreview');
        const photoImg = document.getElementById('photoImg');
        const removePhotoBtn = document.getElementById('removePhotoBtn');

        photoDropZone.addEventListener('click', () => photoInput.click());
        photoDropZone.addEventListener('dragover', (e) => { e.preventDefault(); photoDropZone.classList.add('bg-green-100'); });
        photoDropZone.addEventListener('dragleave', () => photoDropZone.classList.remove('bg-green-100'));
        photoDropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            photoDropZone.classList.remove('bg-green-100');
            if (e.dataTransfer.files.length) photoInput.files = e.dataTransfer.files;
            handlePhotoSelect();
        });
        photoInput.addEventListener('change', handlePhotoSelect);
        function handlePhotoSelect() {
            if (photoInput.files && photoInput.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    photoImg.src = e.target.result;
                    photoPreview.classList.remove('hidden');
                };
                reader.readAsDataURL(photoInput.files[0]);
            }
        }
        removePhotoBtn.addEventListener('click', () => {
             photoInput.value = ''; 
             photoPreview.classList.add('hidden'); 
        });

        // Submit
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Basic Validation Check
            if (userJob === 'sales') {
                 if (parseFloat(document.getElementById('weight_sales').value) <= 0) {
                     Swal.fire('Error', 'Berat beras harus lebih dari 0', 'error');
                     return;
                 }
            }
            if (!photoInput.files || photoInput.files.length === 0) {
                Swal.fire('Error', 'Wajib upload foto', 'error');
                return;
            }

            const formData = new FormData(this);

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Kirim laporan kerja ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memproses...', didOpen: () => Swal.showLoading() });
                    
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
                            Swal.fire('Error', data.message || 'Gagal', 'error');
                        }
                    } catch (err) {
                        Swal.fire('Error', err.message, 'error');
                    }
                }
            });
        });
    </script>
@endsection