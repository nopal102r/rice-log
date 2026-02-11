@extends('layouts.app')

@section('title', 'Dashboard Karyawan')

@section('content')
    <div class="max-w-7xl">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Selamat Datang, {{ auth()->user()->name }}!</h1>
            <p class="text-gray-500">Umur: <span class="font-semibold text-lg text-gray-700">{{ auth()->user()->getAge() }} tahun</span></p>
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

        <!-- Stock Info Widget (for Miller, Packing, Sales) -->
        @if(isset($relevantStock) && $relevantStock)
            <div class="mb-8">
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-6 opacity-5">
                        <i class="fas fa-warehouse text-7xl text-blue-600"></i>
                    </div>
                    <div class="relative z-10">
                        <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-4 flex items-center gap-2">
                            <i class="fas fa-chart-line text-blue-500"></i> Stok Bahan Baku / Produk Tersedia
                        </h4>
                        
                        @if($user->isSales() || $user->isDriver())
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                @foreach($relevantStock as $stock)
                                    <div class="bg-blue-50/30 rounded-xl p-3 border border-blue-200 transition-colors">
                                        <div class="text-[11px] text-blue-400 font-bold uppercase mb-1">{{ str_replace('packed_', '', $stock->name) }}</div>
                                        <div class="text-2xl font-black text-gray-800">{{ number_format($stock->quantity, 0) }} <span class="text-xs font-normal text-gray-400">Krng</span></div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex flex-col md:flex-row md:items-center gap-6">
                                <div class="flex items-end gap-3">
                                    <span class="text-5xl font-black text-blue-600 tracking-tighter">{{ number_format($relevantStock->quantity, 1) }}</span>
                                    <span class="text-gray-400 text-lg mb-1 font-bold">Kg {{ $relevantStock->name === 'gabah' ? 'Gabah' : 'Beras Giling' }}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between text-[10px] mb-2 font-black text-gray-400 uppercase tracking-widest">
                                        <span>Status Stok</span>
                                        <span>{{ min(100, round(($relevantStock->quantity / 1000) * 100)) }}%</span>
                                    </div>
                                    <div class="h-2.5 w-full bg-gray-50 rounded-full overflow-hidden border border-gray-100 p-0.5">
                                        @php
                                            $percent = min(100, ($relevantStock->quantity / 1000) * 100);
                                            $color = $percent > 30 ? 'bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.3)]' : ($percent > 10 ? 'bg-yellow-500' : 'bg-red-500');
                                        @endphp
                                        <div class="h-full {{ $color }} rounded-full transition-all duration-700" style="width: {{ $percent }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="bg-white rounded-2xl shadow-sm p-8 mb-8 border border-gray-100">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Menu Utama</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('employee.absence.form', 'masuk') }}"
                    class="bg-white border-2 border-blue-100 hover:border-blue-500 hover:bg-blue-50 text-blue-600 rounded-2xl p-6 flex flex-col items-center justify-center gap-3 transition-all group">
                    <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <i class="fas fa-sign-in-alt text-xl"></i>
                    </div>
                    <span class="text-xs font-black uppercase tracking-widest text-center">Presensi Masuk</span>
                </a>

                <a href="{{ route('employee.absence.form', 'keluar') }}"
                    class="bg-white border-2 border-indigo-100 hover:border-indigo-500 hover:bg-indigo-50 text-indigo-600 rounded-2xl p-6 flex flex-col items-center justify-center gap-3 transition-all group">
                    <div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all">
                        <i class="fas fa-sign-out-alt text-xl"></i>
                    </div>
                    <span class="text-xs font-black uppercase tracking-widest text-center">Presensi Keluar</span>
                </a>

                <a href="{{ route('employee.leave.create') }}"
                    class="bg-white border-2 border-green-100 hover:border-green-500 hover:bg-green-50 text-green-600 rounded-2xl p-6 flex flex-col items-center justify-center gap-3 transition-all group">
                    <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center group-hover:bg-green-600 group-hover:text-white transition-all">
                        <i class="fas fa-calendar-check text-xl"></i>
                    </div>
                    <span class="text-xs font-black uppercase tracking-widest text-center">Pengajuan Cuti</span>
                </a>

                <a href="{{ route('employee.deposit.create') }}"
                    class="bg-white border-2 border-orange-100 hover:border-orange-500 hover:bg-orange-50 text-orange-600 rounded-2xl p-6 flex flex-col items-center justify-center gap-3 transition-all group">
                    <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-all">
                        <i class="fas fa-shopping-bag text-xl"></i>
                    </div>
                    <span class="text-xs font-black uppercase tracking-widest text-center">Setor Beras</span>
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
                        <span class="text-gray-600">Total Gaji</span>
                        <span class="text-2xl font-bold text-green-600">Rp
                            {{ number_format($depositsData['total_wage'], 0, ',', '.') }}</span>
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