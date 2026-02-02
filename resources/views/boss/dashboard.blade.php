@extends('layouts.app')

@section('title', 'Dashboard Bos')

@section('content')
    <div class="max-w-7xl">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Dashboard Atasan</h1>
            <p class="text-gray-600">Kelola karyawan, absensi, dan gaji mereka</p>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Karyawan</p>
                        <p class="text-4xl font-bold text-gray-800">{{ $totalEmployees }}</p>
                    </div>
                    <i class="fas fa-users text-blue-500 text-4xl opacity-20"></i>
                </div>
            </div>

            <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Karyawan Aktif (Bulan Ini)</p>
                        <p class="text-4xl font-bold text-gray-800">{{ $activeEmployees }}</p>
                    </div>
                    <i class="fas fa-check-circle text-green-500 text-4xl opacity-20"></i>
                </div>
            </div>

            <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Penghasilan Bulan Ini</p>
                        <p class="text-3xl font-bold text-gray-800">Rp {{ number_format($totalMonthlyIncome, 0, ',', '.') }}
                        </p>
                    </div>
                    <i class="fas fa-money-bill-wave text-orange-500 text-4xl opacity-20"></i>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            <a href="{{ route('boss.employees.create') }}"
                class="card-hover bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg p-6 flex items-center justify-center gap-3 hover:shadow-lg">
                <i class="fas fa-user-plus text-2xl"></i>
                <span class="font-bold">Tambah Karyawan</span>
            </a>

            <a href="{{ route('boss.boss-management.create') }}"
                class="card-hover bg-gradient-to-br from-indigo-500 to-indigo-600 text-white rounded-lg p-6 flex items-center justify-center gap-3 hover:shadow-lg">
                <i class="fas fa-user-tie text-2xl"></i>
                <span class="font-bold">Tambah Bos</span>
            </a>

            <a href="{{ route('boss.leave-approval.index') }}"
                class="card-hover bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg p-6 flex items-center justify-center gap-3 hover:shadow-lg">
                <i class="fas fa-calendar-check text-2xl"></i>
                <span class="font-bold">Persetujuan Cuti</span>
            </a>

            <a href="{{ route('boss.deposit-approval.index') }}"
                class="card-hover bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg p-6 flex items-center justify-center gap-3 hover:shadow-lg">
                <i class="fas fa-check-circle text-2xl"></i>
                <span class="font-bold">Verifikasi Setor</span>
            </a>

            <a href="{{ route('boss.payroll-settings.index') }}"
                class="card-hover bg-gradient-to-br from-red-500 to-red-600 text-white rounded-lg p-6 flex items-center justify-center gap-3 hover:shadow-lg">
                <i class="fas fa-cog text-2xl"></i>
                <span class="font-bold">Pengaturan</span>
            </a>
        </div>

        <!-- Pending Approvals -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Pending Leave Submissions -->
            <div class="card-hover bg-white rounded-lg shadow p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-hourglass-half text-yellow-600"></i> Pengajuan Cuti Menunggu
                    <span
                        class="bg-yellow-100 text-yellow-800 text-sm font-bold px-2 py-1 rounded">{{ $pendingLeaves->count() }}</span>
                </h3>

                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($pendingLeaves as $leave)
                        <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4 rounded">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-bold text-gray-800">{{ $leave->user->name }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $leave->start_date->format('d-m-Y') }} s/d {{ $leave->end_date->format('d-m-Y') }}
                                        <span class="font-bold">({{ $leave->getTotalDays() }} hari)</span>
                                    </p>
                                </div>
                                <span
                                    class="bg-yellow-200 text-yellow-800 text-xs font-bold px-2 py-1 rounded">{{ $leave->status }}</span>
                            </div>
                            @if($leave->reason)
                                <p class="text-sm text-gray-700 mb-2">Alasan: {{ $leave->reason }}</p>
                            @endif
                            <div class="flex gap-2">
                                <button onclick="approveLeave({{ $leave->id }})"
                                    class="text-xs bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded">
                                    <i class="fas fa-check mr-1"></i> Setujui
                                </button>
                                <button onclick="openRejectModal({{ $leave->id }})"
                                    class="text-xs bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded">
                                    <i class="fas fa-times mr-1"></i> Tolak
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 py-4">Tidak ada pengajuan cuti yang menunggu</p>
                    @endforelse
                </div>

                <a href="{{ route('boss.leave-approval.index') }}"
                    class="mt-4 block text-center text-blue-600 hover:text-blue-800 font-bold text-sm">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <!-- Pending Deposits -->
            <div class="card-hover bg-white rounded-lg shadow p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-hourglass-end text-purple-600"></i> Setor Menunggu Verifikasi
                    <span
                        class="bg-purple-100 text-purple-800 text-sm font-bold px-2 py-1 rounded">{{ $pendingDeposits->count() }}</span>
                </h3>

                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($pendingDeposits as $deposit)
                        <div class="border-l-4 border-purple-400 bg-purple-50 p-4 rounded">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-bold text-gray-800">{{ $deposit->user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $deposit->weight }} kg - Rp
                                        {{ number_format($deposit->total_price, 0, ',', '.') }}</p>
                                </div>
                                <span
                                    class="bg-purple-200 text-purple-800 text-xs font-bold px-2 py-1 rounded">{{ $deposit->status }}</span>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="verifyDeposit({{ $deposit->id }})"
                                    class="text-xs bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded">
                                    <i class="fas fa-check mr-1"></i> Verifikasi
                                </button>
                                <button onclick="openRejectDepositModal({{ $deposit->id }})"
                                    class="text-xs bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded">
                                    <i class="fas fa-times mr-1"></i> Tolak
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 py-4">Tidak ada setor yang menunggu verifikasi</p>
                    @endforelse
                </div>

                <a href="{{ route('boss.deposit-approval.index') }}"
                    class="mt-4 block text-center text-blue-600 hover:text-blue-800 font-bold text-sm">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Employee List Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Rekapitulasi Karyawan (Bulan Ini)</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-700 font-bold">Nama</th>
                            <th class="px-4 py-2 text-left text-gray-700 font-bold">Umur</th>
                            <th class="px-4 py-2 text-left text-gray-700 font-bold">Status</th>
                            <th class="px-4 py-2 text-center text-gray-700 font-bold">Hadir</th>
                            <th class="px-4 py-2 text-center text-gray-700 font-bold">Kg Setor</th>
                            <th class="px-4 py-2 text-right text-gray-700 font-bold">Gaji (Rp)</th>
                            <th class="px-4 py-2 text-center text-gray-700 font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employeeSummaries as $item)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2 font-bold text-gray-800">{{ $item['user']->name }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ $item['user']->getAge() }} tahun</td>
                                <td class="px-4 py-2">
                                    <span
                                        class="px-2 py-1 rounded text-xs font-bold {{ $item['summary']->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $item['summary']->status === 'active' ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-center font-bold">{{ $item['summary']->days_present }}</td>
                                <td class="px-4 py-2 text-center font-bold text-orange-600">
                                    {{ $item['summary']->total_kg_deposited }}</td>
                                <td class="px-4 py-2 text-right font-bold text-green-600">Rp
                                    {{ number_format($item['summary']->total_salary, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-center">
                                    <a href="{{ route('boss.employees.show', $item['user']->id) }}"
                                        class="text-blue-600 hover:text-blue-800 font-bold text-sm">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-gray-400">Belum ada data karyawan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        function approveLeave(submissionId) {
            Swal.fire({
                title: 'Setujui Pengajuan Cuti?',
                text: 'Karyawan akan menerima notifikasi bahwa cuti mereka telah disetujui.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ url("boss/leave-approval") }}/' + submissionId + '/approve', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                        }
                    }).then(r => r.json()).then(data => {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                    });
                }
            });
        }

        function openRejectModal(submissionId) {
            Swal.fire({
                title: 'Tolak Pengajuan Cuti',
                input: 'textarea',
                inputLabel: 'Alasan Penolakan',
                inputPlaceholder: 'Masukkan alasan penolakan...',
                showCancelButton: true,
                confirmButtonText: 'Tolak',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    fetch('{{ url("boss/leave-approval") }}/' + submissionId + '/reject', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ rejection_reason: result.value })
                    }).then(r => r.json()).then(data => {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                    });
                }
            });
        }

        function verifyDeposit(depositId) {
            Swal.fire({
                title: 'Verifikasi Setor?',
                text: 'Karyawan akan menerima notifikasi bahwa setor mereka telah diverifikasi.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Verifikasi!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ url("boss/deposit-approval") }}/' + depositId + '/verify', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                        }
                    }).then(r => r.json()).then(data => {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                    });
                }
            });
        }

        function openRejectDepositModal(depositId) {
            Swal.fire({
                title: 'Tolak Setor',
                input: 'textarea',
                inputLabel: 'Alasan Penolakan',
                inputPlaceholder: 'Masukkan alasan penolakan...',
                showCancelButton: true,
                confirmButtonText: 'Tolak',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    fetch('{{ url("boss/deposit-approval") }}/' + depositId + '/reject', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ reason: result.value })
                    }).then(r => r.json()).then(data => {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                    });
                }
            });
        }
    </script>
@endsection