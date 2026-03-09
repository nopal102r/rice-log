@extends('layouts.app')

@section('title', 'Detail Kehadiran - ' . $user->name)

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('boss.reports.attendance') }}" class="inline-flex items-center text-blue-600 font-black hover:bg-blue-50 px-4 py-2 rounded-xl transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Laporan Harian
            </a>
        </div>

        <!-- Employee Info Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-8 text-white">
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <div class="h-24 w-24 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-4xl font-black shadow-inner border border-white/30">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div class="text-center md:text-left">
                        <h1 class="text-4xl font-black mb-1">{{ $user->name }}</h1>
                        <p class="text-blue-100 font-bold uppercase tracking-[0.2em] text-sm">{{ $user->job }} &bull; {{ $user->email }}</p>
                    </div>
                    <div class="md:ml-auto flex gap-4">
                        <div class="bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/20 text-center min-w-[120px]">
                            <p class="text-[10px] font-black uppercase text-blue-200 mb-1">Total Hadir</p>
                            <p class="text-3xl font-black">{{ $totalPresent }}</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/20 text-center min-w-[120px]">
                            <p class="text-[10px] font-black uppercase text-blue-200 mb-1">Efektivitas</p>
                            <p class="text-3xl font-black">{{ count($dailyData) > 0 ? round(($totalPresent / count($dailyData)) * 100) : 0 }}%</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="p-6 border-b border-gray-100 flex flex-wrap items-center justify-between gap-4">
                <h2 class="text-xl font-black text-gray-800">
                    Rekap Bulan: <span class="text-blue-600">{{ DateTime::createFromFormat('!m', $currentMonth)->format('F') }} {{ $currentYear }}</span>
                </h2>
                
                <form action="{{ route('boss.reports.attendance.detail', $user->id) }}" method="GET" class="flex items-center gap-3">
                    <select name="month" class="border border-gray-200 rounded-xl px-4 py-2.5 font-bold outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                    <select name="year" class="border border-gray-200 rounded-xl px-4 py-2.5 font-bold outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                        @foreach(range(date('Y')-1, date('Y')+1) as $y)
                            <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-gray-900 text-white px-6 py-2.5 rounded-xl font-black shadow-lg hover:bg-black transition active:scale-95">
                        Filter
                    </button>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-8 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Tanggal</th>
                            <th class="px-8 py-4 text-center text-xs font-black text-gray-400 uppercase tracking-widest">Jam Masuk</th>
                            <th class="px-8 py-4 text-center text-xs font-black text-gray-400 uppercase tracking-widest">Jam Keluar</th>
                            <th class="px-8 py-4 text-center text-xs font-black text-gray-400 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-4 text-center text-xs font-black text-gray-400 uppercase tracking-widest">Jarak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-bold">
                        @foreach($dailyData as $day => $data)
                            <tr class="{{ $data['date']->isToday() ? 'bg-blue-50/50' : '' }} {{ $data['date']->isWeekend() ? 'bg-gray-50/30' : '' }}">
                                <td class="px-8 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <span class="w-8 text-center text-lg font-black {{ $data['date']->isWeekend() ? 'text-red-400' : 'text-gray-400' }}">
                                            {{ str_pad($day, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <span class="text-sm uppercase tracking-tighter {{ $data['date']->isWeekend() ? 'text-red-500' : 'text-gray-600' }}">
                                            {{ $data['date']->format('l') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-4 text-center whitespace-nowrap">
                                    @if($data['in'] !== '-')
                                        <span class="text-green-600 font-black text-lg">{{ $data['in'] }}</span>
                                    @else
                                        <span class="text-gray-200 font-black">-</span>
                                    @endif
                                </td>
                                <td class="px-8 py-4 text-center whitespace-nowrap">
                                    @if($data['out'] !== '-')
                                        <span class="text-red-500 font-black text-lg">{{ $data['out'] }}</span>
                                    @else
                                        <span class="text-gray-200 font-black">-</span>
                                    @endif
                                </td>
                                <td class="px-8 py-4 text-center whitespace-nowrap">
                                    @if($data['status'] !== '-')
                                        <span class="px-3 py-1 text-[10px] font-black rounded-full shadow-sm border
                                            {{ $data['status'] === 'hadir' ? 'bg-green-100 text-green-700 border-green-200' : 
                                               ($data['status'] === 'sakit' ? 'bg-orange-100 text-orange-700 border-orange-200' : 'bg-blue-100 text-blue-700 border-blue-200') }} uppercase tracking-widest">
                                            {{ $data['status'] }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">T/A</span>
                                    @endif
                                </td>
                                <td class="px-8 py-4 text-center whitespace-nowrap text-gray-500 text-sm">
                                    {{ $data['distance'] !== '-' ? $data['distance'] . ' km' : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
