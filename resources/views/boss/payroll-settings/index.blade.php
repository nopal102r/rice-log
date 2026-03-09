@extends('layouts.app')

@section('title', 'Pengaturan Gaji')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Pengaturan Gaji & Sistem</h1>
            <p class="text-gray-600">Atur parameter gaji untuk setiap posisi dan aturan sistem.</p>
        </div>

        <div class="bg-white rounded-lg shadow p-8">
            <form id="settingsForm">
                @csrf

                <!-- Wage Settings -->
                <div class="mb-8 pb-8 border-b border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-money-bill-wave text-green-600"></i> Pengaturan Upah
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Drivers -->
                        <div>
                            <label class="block text-gray-800 font-bold mb-2">
                                Upah Supir (per kg)
                            </label>
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-bold text-gray-600">Rp</span>
                                <input type="number" name="driver_rate_per_kg" id="driver_rate_per_kg" value="{{ $settings->driver_rate_per_kg }}"
                                    required step="50" min="0"
                                    class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-green-500"
                                    placeholder="500">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Upah supir per kg beras yang dikirim.</p>
                        </div>

                        <!-- Millers -->
                        <div>
                            <label class="block text-gray-800 font-bold mb-2">
                                Upah Penggiling (per kg)
                            </label>
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-bold text-gray-600">Rp</span>
                                <input type="number" name="miller_rate_per_kg" id="miller_rate_per_kg" value="{{ $settings->miller_rate_per_kg }}"
                                    required step="50" min="0"
                                    class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-green-500"
                                    placeholder="200">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Upah penggiling per kg beras yang digiling.</p>
                        </div>

                        <!-- Farmers Box -->
                        <div>
                            <label class="block text-gray-800 font-bold mb-2">
                                Upah Petani (per Kotak Lahan)
                            </label>
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-bold text-gray-600">Rp</span>
                                <input type="number" name="farmer_rate_per_box" id="farmer_rate_per_box" value="{{ $settings->farmer_rate_per_box }}"
                                    required step="1000" min="0"
                                    class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-green-500"
                                    placeholder="50000">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Upah petani per kotak lahan yang diurus.</p>
                        </div>

                        <!-- Sales Commission -->
                        <div>
                            <label class="block text-gray-800 font-bold mb-2">
                                Komisi Sales (%) / Cadangan
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="sales_commission_rate" id="sales_commission_rate" value="{{ $settings->sales_commission_rate }}"
                                    required step="0.1" min="0" max="100"
                                    class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-green-500"
                                    placeholder="5">
                                <span class="text-lg font-bold text-gray-600">%</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Persentase komisi (opsional/alternatif).</p>
                        </div>

                        <!-- Packing -->
                        <div>
                            <label class="block text-gray-800 font-bold mb-2">
                                Upah Packing (per kg)
                            </label>
                            <div class="flex items-center gap-2">
                                <span class="text-lg font-bold text-gray-600">Rp</span>
                                <input type="number" name="packing_rate_per_kg" id="packing_rate_per_kg" value="{{ $settings->packing_rate_per_kg }}"
                                    required step="1" min="0"
                                    class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-green-500"
                                    placeholder="20">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Upah tukang packing per kg beras yang dipacking.</p>
                        </div>
                    </div>

                     <div class="mt-6">
                        <label class="block text-gray-800 font-bold mb-2">
                            Harga Jual Beras Standar (per kg)
                        </label>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-bold text-gray-600">Rp</span>
                            <input type="number" name="price_per_kg" id="price_per_kg" value="{{ $settings->price_per_kg }}"
                                required step="100" min="0"
                                class="flex-1 border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-green-500 text-lg"
                                placeholder="Contoh: 30000">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Harga dasar untuk perhitungan pendapatan/setoran beras.
                        </p>
                    </div>
                </div>

                <!-- Office Location -->
                <div class="mb-8 pb-8 border-b border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-red-600"></i> Lokasi Kantor Pusat
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-800 font-bold mb-2">
                                Latitude (Lintang)
                            </label>
                            <input type="number" name="office_latitude" id="office_latitude"
                                value="{{ $settings->office_latitude }}" required step="0.0001" min="-90" max="90"
                                class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-red-500"
                                placeholder="Contoh: -6.2088">
                        </div>

                        <div>
                            <label class="block text-gray-800 font-bold mb-2">
                                Longitude (Bujur)
                            </label>
                            <input type="number" name="office_longitude" id="office_longitude"
                                value="{{ $settings->office_longitude }}" required step="0.0001" min="-180" max="180"
                                class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-red-500"
                                placeholder="Contoh: 106.8456">
                        </div>
                    </div>
                </div>

                <!-- Distance & Attendance Rules -->
                <div class="mb-8 pb-8 border-b border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-ruler-combined text-blue-600"></i> Aturan Kehadiran
                    </h3>

                    <div class="mb-6">
                        <label class="block text-gray-800 font-bold mb-2">
                            Jarak Maksimal dari Kantor (km)
                        </label>
                        <input type="number" name="max_distance_allowed" id="max_distance_allowed"
                            value="{{ $settings->max_distance_allowed }}" required step="0.1" min="0.1"
                            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-500"
                            placeholder="Contoh: 2">
                        <p class="text-xs text-gray-500 mt-1">
                            Jika karyawan absen lebih dari jarak ini, akan ada warning. Default: 2 km
                        </p>
                    </div>
                </div>

                <!-- Leave & Deposit Rules -->
                <div class="mb-8 pb-8 border-b border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-calendar-check text-purple-600"></i> Aturan Cuti & Setor
                    </h3>

                    <div class="mb-6">
                        <label class="block text-gray-800 font-bold mb-2">
                            Jumlah Hari Cuti per Bulan
                        </label>
                        <input type="number" name="leave_days_per_month" id="leave_days_per_month"
                            value="{{ $settings->leave_days_per_month }}" required min="1" max="30"
                            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-purple-500"
                            placeholder="Contoh: 3">
                        <p class="text-xs text-gray-500 mt-1">
                            Maksimal hari cuti yang bisa diajukan karyawan per bulan. Default: 3 hari
                        </p>
                    </div>

                    <div>
                        <label class="block text-gray-800 font-bold mb-2">
                            Minimal Setor per Minggu
                        </label>
                        <input type="number" name="min_deposit_per_week" id="min_deposit_per_week"
                            value="{{ $settings->min_deposit_per_week }}" required min="1" max="7"
                            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-purple-500"
                            placeholder="Contoh: 1">
                        <p class="text-xs text-gray-500 mt-1">
                            Jumlah minimal setor yang harus dilakukan karyawan per minggu. Default: 1 kali
                        </p>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col md:flex-row gap-4">
                    <button type="submit"
                        class="flex-[2] bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-3 rounded-lg flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Simpan Pengaturan
                    </button>
                    <a href="{{ route('boss.dashboard') }}"
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
        document.getElementById('settingsForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = {
                price_per_kg: document.getElementById('price_per_kg').value,
                driver_rate_per_kg: document.getElementById('driver_rate_per_kg').value,
                miller_rate_per_kg: document.getElementById('miller_rate_per_kg').value,
                farmer_rate_per_box: document.getElementById('farmer_rate_per_box').value,
                sales_commission_rate: document.getElementById('sales_commission_rate').value,
                office_latitude: document.getElementById('office_latitude').value,
                office_longitude: document.getElementById('office_longitude').value,
                max_distance_allowed: document.getElementById('max_distance_allowed').value,
                leave_days_per_month: document.getElementById('leave_days_per_month').value,
                min_deposit_per_week: document.getElementById('min_deposit_per_week').value,
                packing_rate_per_kg: document.getElementById('packing_rate_per_kg').value,
            };

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Simpan pengaturan gaji dan sistem?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan!',
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
                        const response = await fetch('{{ route("boss.payroll-settings.update") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            },
                            body: JSON.stringify(formData)
                        });

                        const data = await response.json();

                        if (data.success) {
                            Swal.fire('Berhasil!', data.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.message || 'Terjadi kesalahan', 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'Terjadi kesalahan: ' + error.message, 'error');
                    }
                }
            });
        });
    </script>
@endsection