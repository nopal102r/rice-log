@extends('layouts.app')

@section('title', 'Dashboard Karyawan')

@section('content')
    <div class="max-w-7xl">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Selamat Datang, {{ auth()->user()->name }}!</h1>
            <p class="text-gray-600">Umur: <span class="font-bold text-lg">{{ auth()->user()->getAge() }} tahun</span></p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Hadir</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['hadir'] }}</p>
                    </div>
                    <i class="fas fa-check-circle text-blue-500 text-4xl opacity-20"></i>
                </div>
            </div>

            <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Sakit</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['sakit'] }}</p>
                    </div>
                    <i class="fas fa-hospital-user text-red-500 text-4xl opacity-20"></i>
                </div>
            </div>

            <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Izin</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['izin'] }}</p>
                    </div>
                    <i class="fas fa-file-alt text-yellow-500 text-4xl opacity-20"></i>
                </div>
            </div>

            <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Cuti</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['cuti'] }}</p>
                    </div>
                    <i class="fas fa-calendar-alt text-green-500 text-4xl opacity-20"></i>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-lg shadow p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Menu Absensi & Setor</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('employee.absence.form', 'masuk') }}"
                    class="card-hover gradient-bg text-white rounded-lg p-6 flex flex-col items-center justify-center hover:shadow-lg">
                    <i class="fas fa-sign-in-alt text-4xl mb-2"></i>
                    <span class="font-bold text-center">Presensi Masuk</span>
                </a>

                <a href="{{ route('employee.absence.form', 'keluar') }}"
                    class="card-hover bg-gradient-to-br from-indigo-400 to-blue-500 text-white rounded-lg p-6 flex flex-col items-center justify-center hover:shadow-lg">
                    <i class="fas fa-sign-out-alt text-4xl mb-2"></i>
                    <span class="font-bold text-center">Presensi Keluar</span>
                </a>

                <a href="{{ route('employee.leave.create') }}"
                    class="card-hover bg-gradient-to-br from-green-400 to-teal-500 text-white rounded-lg p-6 flex flex-col items-center justify-center hover:shadow-lg">
                    <i class="fas fa-calendar-check text-4xl mb-2"></i>
                    <span class="font-bold text-center">Pengajuan Cuti</span>
                </a>

                <a href="{{ route('employee.deposit.create') }}"
                    class="card-hover bg-gradient-to-br from-orange-400 to-red-500 text-white rounded-lg p-6 flex flex-col items-center justify-center hover:shadow-lg">
                    <i class="fas fa-shopping-bag text-4xl mb-2"></i>
                    <span class="font-bold text-center">Setor Beras</span>
                </a>
            </div>
        </div>

        <!-- Deposits Summary -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="card-hover bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-shopping-bag text-orange-500"></i> Rekapitulasi Setor (Bulan Ini)
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center border-b pb-2">
                        <span class="text-gray-600">Total Kg</span>
                        <span class="text-2xl font-bold text-orange-600">{{ $depositsData['total_kg'] }} kg</span>
                    </div>
                    <div class="flex justify-between items-center border-b pb-2">
                        <span class="text-gray-600">Total Harga</span>
                        <span class="text-2xl font-bold text-green-600">Rp
                            {{ number_format($depositsData['total_price'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Jumlah Setor</span>
                        <span class="text-2xl font-bold text-blue-600">{{ $depositsData['count'] }}x</span>
                    </div>
                </div>
            </div>

            <div class="card-hover bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-history text-purple-500"></i> Aktivitas Terakhir
                </h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @forelse($recentActivities['absences'] as $absence)
                        <div class="flex justify-between items-center text-sm border-b pb-2">
                            <span class="text-gray-600">
                                @if($absence->type === 'masuk')
                                    <i class="fas fa-sign-in-alt text-blue-500"></i> Masuk
                                @else
                                    <i class="fas fa-sign-out-alt text-red-500"></i> Keluar
                                @endif
                                <strong>({{ $absence->status ?? 'N/A' }})</strong>
                            </span>
                            <span class="text-xs text-gray-400">{{ $absence->created_at->format('d-m-Y H:i') }}</span>
                        </div>
                    @empty
                        <p class="text-gray-400 text-center text-sm">Belum ada aktivitas</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        // Load notifications
        function loadNotifications() {
            $.get('/api/notifications', function (data) {
                const count = data.notifications.length;
                const badge = $('#notification-count');
                
                if (count > 0) {
                    badge.text(count).removeClass('hidden');
                    let html = '';
                    data.notifications.forEach(notif => {
                        html += `
                            <div class="border-b py-2 px-4 last:border-b-0">
                                <p class="font-bold text-sm">${notif.title}</p>
                                <p class="text-xs text-gray-600">${notif.message}</p>
                                <p class="text-xs text-gray-400 mt-1">${notif.created_at}</p>
                            </div>
                        `;
                    });
                    $('#notification-list').html(html);
                } else {
                    badge.addClass('hidden');
                }
            });
        }

        // Load notifications on page load
        loadNotifications();

        // Refresh notifications every 30 seconds
        setInterval(loadNotifications, 30000);
    </script>
@endsection