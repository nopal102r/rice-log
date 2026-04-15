@extends('layouts.app')

@section('title', 'Leaderboard Integritas')

@section('content')
<div class="max-w-5xl mx-auto pb-12">
    <div class="mb-8 text-center pt-4">
        <h1 class="text-4xl md:text-5xl font-black text-gray-800 mb-3 tracking-tight">🏆 Papan Peringkat Integritas 🏆</h1>
        <p class="text-gray-600 font-medium">Rank bulanan karyawan berdasarkan performa absen & poin.</p>
    </div>

    <!-- Filter Bulan & Tahun -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-6 mb-8 flex flex-col md:flex-row items-center justify-between gap-4 relative z-10">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Filter Periode</h2>
            <p class="text-sm text-gray-500">Statistik dan Mutasi akan menyesuaikan bulan yang dipilih.</p>
        </div>
        <form method="GET" action="{{ route('boss.wallet.leaderboard') }}" class="flex items-center gap-3 w-full md:w-auto">
            <select name="month" class="rounded-xl border-gray-300 bg-gray-50 text-gray-700 focus:ring-blue-500 focus:border-blue-500 font-semibold px-4">
                @for($m=1; $m<=12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                    </option>
                @endfor
            </select>
            <select name="year" class="rounded-xl border-gray-300 bg-gray-50 text-gray-700 focus:ring-blue-500 focus:border-blue-500 font-semibold px-4">
                @for($y=date('Y')-2; $y<=date('Y')+1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white rounded-xl px-6 py-2 font-bold shadow-sm transition">
                Filter
            </button>
        </form>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden relative">
        <div class="p-6 bg-gradient-to-r from-blue-700 via-indigo-600 to-purple-600 text-white flex justify-between items-center relative overflow-hidden">
            <div class="absolute inset-0 bg-black/10"></div>
            
            <h2 class="text-2xl font-black relative z-10 flex items-center shadow-sm">
                <i class="fas fa-medal mr-3 text-yellow-300"></i> Peringkat Global
            </h2>
            <span class="bg-white/20 backdrop-blur-md px-4 py-2 rounded-full text-sm font-black tracking-widest relative z-10 border border-white/20 shadow-inner">
                {{ $users->count() }} KARYAWAN
            </span>
        </div>
        
        <div class="divide-y divide-gray-100">
            @forelse($users as $index => $user)
            <!-- Clickable Row to open modal -->
            <div onclick="openMutationsModal({{ $user->id }})" class="p-6 cursor-pointer flex flex-col md:flex-row items-start md:items-center justify-between hover:bg-blue-50 transition group">
                <div class="flex items-center gap-6 w-full md:w-auto mb-4 md:mb-0">
                    <!-- Rank Badge -->
                    <div class="w-14 h-14 shrink-0 rounded-2xl flex items-center justify-center font-black text-2xl shadow-sm border
                        {{ $index === 0 ? 'bg-gradient-to-br from-yellow-300 to-yellow-500 text-white border-yellow-200 shadow-yellow-200' : 
                          ($index === 1 ? 'bg-gradient-to-br from-gray-200 to-gray-400 text-white border-gray-200 shadow-gray-200' : 
                          ($index === 2 ? 'bg-gradient-to-br from-orange-300 to-orange-500 text-white border-orange-200 shadow-orange-200' : 
                          'bg-gray-50 text-gray-500 border-gray-200')) }}">
                        @if($index < 3)
                            <i class="fas fa-trophy absolute opacity-20 text-4xl"></i>
                        @endif
                        <span class="relative z-10">{{ $index + 1 }}</span>
                    </div>
                    
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-xl font-bold text-gray-800">{{ $user->name }}</h3>
                            <i class="fas fa-chevron-right text-gray-300 group-hover:text-blue-500 transition text-xs opacity-0 group-hover:opacity-100 transform translate-x-[-10px] group-hover:translate-x-0"></i>
                        </div>
                        <p class="text-xs text-gray-500 uppercase tracking-widest font-black mt-1">{{ $user->role }} | {{ $user->job ?? 'Umum' }}</p>
                        
                        <div class="flex items-center gap-3 mt-3">
                            <span class="bg-gray-100 border border-gray-200 text-gray-600 text-[10px] font-bold px-2 py-1 rounded-md" title="Poin yang didapatkan bulan ini">
                                <i class="fas fa-arrow-down text-green-500 mr-1"></i> Earned: {{ $user->monthly_earned }}
                            </span>
                            <span class="bg-gray-100 border border-gray-200 text-gray-600 text-[10px] font-bold px-2 py-1 rounded-md" title="Poin yang ditukar saldo bulan ini">
                                <i class="fas fa-ticket-alt text-purple-500 mr-1"></i> Terpakai: {{ $user->monthly_spent }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="text-left md:text-right w-full md:w-auto bg-gray-50 group-hover:bg-white md:bg-transparent rounded-2xl p-4 md:p-0 border border-gray-100 md:border-none transition">
                    <div class="flex md:flex-col justify-between items-center md:items-end">
                        <p class="text-sm text-gray-500 font-bold uppercase tracking-widest mb-1">Skor Bulan Ini</p>
                        <p class="text-4xl font-black text-blue-600 mb-2">
                            {{ $user->monthly_score }}
                        </p>
                    </div>
                    <div class="h-px bg-gray-200 w-full mb-2 hidden md:block"></div>
                    <p class="text-xs font-bold text-gray-600">Total Keseluruhan Saldo: <span class="font-black text-green-600">{{ $user->current_points }} PTS</span></p>
                </div>
            </div>

            <!-- Modal Specific for each User -->
            <div id="modal-user-{{ $user->id }}" class="hidden fixed inset-0 z-50 bg-gray-900/40 backdrop-blur-sm items-center justify-center p-4">
                <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="modal-content-{{ $user->id }}">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800">Mutasi Mutlak: <span class="text-blue-600">{{ $user->name }}</span></h3>
                            <p class="text-xs text-gray-500 uppercase tracking-widest font-bold">Bulan ke-{{ $month }}, Tahun {{ $year }}</p>
                        </div>
                        <button type="button" onclick="closeMutationsModal({{ $user->id }})" class="text-gray-400 hover:text-gray-600 bg-white shadow-sm w-10 h-10 rounded-full flex items-center justify-center hover:bg-gray-200 transition">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    
                    <div class="p-0 max-h-[60vh] overflow-y-auto">
                        @if($user->monthLedgers->count() > 0)
                            <div class="divide-y divide-gray-100">
                                @foreach($user->monthLedgers as $tx)
                                <div class="p-4 px-6 flex items-center justify-between hover:bg-gray-50 transition border-l-4 {{ $tx->amount > 0 ? 'border-l-green-500' : 'border-l-red-500' }}">
                                    <div class="flex items-center gap-4">
                                        @if($tx->amount > 0)
                                        <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center shrink-0 border border-green-100 shadow-sm">
                                            <i class="fas fa-arrow-down"></i>
                                        </div>
                                        @else
                                        <div class="w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center shrink-0 border border-red-100 shadow-sm">
                                            <i class="fas fa-arrow-up"></i>
                                        </div>
                                        @endif
                                        
                                        <div>
                                            <p class="font-bold text-gray-800 text-sm md:text-base">{{ $tx->description }}</p>
                                            <p class="text-xs text-gray-500 mt-1 font-bold">{{ $tx->created_at->format('d M Y, H:i:s') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="font-black text-xl {{ $tx->amount > 0 ? 'text-green-600' : 'text-red-500' }}">
                                            {{ $tx->amount > 0 ? '+' : '' }}{{ $tx->amount }}
                                        </p>
                                        <p class="text-[10px] uppercase font-bold text-gray-400">Bal: {{ $tx->current_balance }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-16 text-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300 text-4xl">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <p class="text-gray-500 font-bold">Tidak ada riwayat mutasi di bulan ini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-gray-500 font-bold bg-gray-50">
                Data tidak ditemukan.
            </div>
            @endforelse
        </div>
    </div>
</div>

@endsection

@section('extra-js')
<script>
    function openMutationsModal(userId) {
        const modal = document.getElementById('modal-user-' + userId);
        const content = document.getElementById('modal-content-' + userId);
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden'); // Prevent background scroll
        
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeMutationsModal(userId) {
        const modal = document.getElementById('modal-user-' + userId);
        const content = document.getElementById('modal-content-' + userId);
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        document.body.classList.remove('overflow-hidden');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
</script>
@endsection
