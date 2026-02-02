@extends('layouts.app')

@section('title', 'Pengajuan Cuti')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Pengajuan Cuti</h1>
            <p class="text-gray-600">Ajukan cuti Anda dengan mudah. Maksimal 3 hari per bulan.</p>
        </div>

        <div class="bg-white rounded-lg shadow p-8">
            <form id="leaveForm">
                @csrf

                <div class="mb-6">
                    <label class="block text-gray-800 font-bold mb-2">
                        <i class="fas fa-calendar text-blue-600 mr-2"></i> Tanggal Mulai Cuti
                    </label>
                    <input type="date" name="start_date" id="start_date" required
                        class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-500"
                        min="{{ date('Y-m-d') }}">
                    <p class="text-xs text-gray-500 mt-1">Pilih tanggal mulai cuti (tidak boleh tanggal lalu)</p>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-800 font-bold mb-2">
                        <i class="fas fa-calendar-alt text-blue-600 mr-2"></i> Tanggal Selesai Cuti
                    </label>
                    <input type="date" name="end_date" id="end_date" required
                        class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-500"
                        min="{{ date('Y-m-d') }}">
                    <p class="text-xs text-gray-500 mt-1">Pilih tanggal berakhir cuti</p>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-800 font-bold mb-2">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i> Total Hari Cuti
                    </label>
                    <div class="bg-blue-50 border border-blue-200 rounded px-4 py-3">
                        <p id="totalDays" class="text-2xl font-bold text-blue-600">0 hari</p>
                        <p class="text-xs text-gray-600 mt-2">Perhitungan otomatis berdasarkan tanggal yang dipilih</p>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-800 font-bold mb-2">
                        <i class="fas fa-comment text-blue-600 mr-2"></i> Alasan / Keterangan
                    </label>
                    <textarea name="reason" id="reason" rows="5"
                        class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-blue-500"
                        placeholder="Jelaskan alasan pengajuan cuti Anda..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">Opsional, tetapi akan membantu atasan memahami permintaan Anda</p>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mb-6">
                    <p class="text-yellow-800 text-sm">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian:</strong> Pengajuan cuti dibatasi hingga 3 hari per bulan.
                        Pengajuan Anda akan dikirim ke atasan untuk disetujui atau ditolak.
                    </p>
                </div>

                <div class="flex gap-4">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 rounded-lg flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i> Ajukan Cuti
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
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const totalDaysDisplay = document.getElementById('totalDays');

        function calculateDays() {
            if (startDateInput.value && endDateInput.value) {
                const start = new Date(startDateInput.value);
                const end = new Date(endDateInput.value);
                const diffTime = end - start;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

                if (diffDays > 0) {
                    totalDaysDisplay.textContent = diffDays + ' hari';
                }
            }
        }

        startDateInput.addEventListener('change', calculateDays);
        endDateInput.addEventListener('change', calculateDays);

        document.getElementById('leaveForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const startDate = startDateInput.value;
            const endDate = endDateInput.value;
            const reason = document.getElementById('reason').value;

            if (!startDate || !endDate) {
                Swal.fire('Error', 'Silakan pilih tanggal mulai dan selesai cuti', 'error');
                return;
            }

            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;

            if (diffDays <= 0) {
                Swal.fire('Error', 'Tanggal selesai harus setelah tanggal mulai', 'error');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Ajukan cuti selama ' + diffDays + ' hari?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ajukan!',
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
                        const response = await fetch('{{ route("employee.leave.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            },
                            body: JSON.stringify({
                                start_date: startDate,
                                end_date: endDate,
                                reason: reason
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            Swal.fire('Berhasil!', data.message, 'success').then(() => {
                                window.location.href = '{{ route("employee.leave.my-submissions") }}';
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