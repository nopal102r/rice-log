@extends('layouts.app')

@section('title', 'Laporan Kehadiran Harian')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header & Nav -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h1 class="text-3xl font-black text-gray-900 mb-2">Laporan Kehadiran</h1>
                <p class="text-gray-500 font-medium">Monitoring kehadiran karyawan per hari.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('boss.reports.attendance', ['date' => $prevDate]) }}" 
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 p-3 rounded-xl transition shadow-sm">
                    <i class="fas fa-chevron-left"></i>
                </a>
                
                <div class="bg-blue-50 px-6 py-3 rounded-xl border border-blue-100 flex items-center gap-3 shadow-inner">
                    <i class="fas fa-calendar-day text-blue-600 text-lg"></i>
                    <span class="text-lg font-black text-blue-900">{{ $currentDate->format('d F Y') }}</span>
                </div>

                <a href="{{ route('boss.reports.attendance', ['date' => $nextDate]) }}" 
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 p-3 rounded-xl transition shadow-sm">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>

            <form action="{{ route('boss.reports.attendance') }}" method="GET" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ $currentDate->format('Y-m-d') }}" 
                       class="border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition font-bold text-gray-700 shadow-sm">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-xl shadow-lg shadow-blue-200 transition active:scale-95">
                    Go
                </button>
            </form>
        </div>

        <!-- Attendance List -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b-2 border-gray-100">
                        <tr>
                            <th class="px-8 py-5 text-left text-sm font-bold text-gray-500 uppercase tracking-widest">Karyawan</th>
                            <th class="px-8 py-5 text-center text-sm font-bold text-gray-500 uppercase tracking-widest">Jabatan</th>
                            <th class="px-8 py-5 text-center text-sm font-bold text-gray-500 uppercase tracking-widest">Jam Masuk</th>
                            <th class="px-8 py-5 text-center text-sm font-bold text-gray-500 uppercase tracking-widest">Jam Keluar</th>
                            <th class="px-8 py-5 text-center text-sm font-bold text-gray-500 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-5 text-center text-sm font-bold text-gray-500 uppercase tracking-widest">Jarak (Km)</th>
                            <th class="px-8 py-5 text-right text-sm font-bold text-gray-500 uppercase tracking-widest">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($reportData as $data)
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg ring-4 ring-white shadow-sm transition-transform group-hover:scale-110">
                                            {{ substr($data->user->name, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-lg font-bold text-gray-900 leading-none mb-1 group-hover:text-blue-700 transition-colors">{{ $data->user->name }}</div>
                                            <div class="text-xs font-semibold text-gray-400 uppercase tracking-tighter">{{ $data->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-center whitespace-nowrap">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-600 uppercase tracking-wider">
                                        {{ $data->user->job }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-center whitespace-nowrap">
                                    @if($data->in !== '-')
                                        <div class="inline-flex items-center gap-2 bg-green-50 text-green-700 px-3 py-1.5 rounded-lg border border-green-100 font-bold text-sm">
                                            <i class="fas fa-sign-in-alt"></i>
                                            {{ $data->in }}
                                        </div>
                                    @else
                                        <span class="text-gray-300 font-bold">-</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-center whitespace-nowrap">
                                    @if($data->out !== '-')
                                        <div class="inline-flex items-center gap-2 bg-red-50 text-red-700 px-3 py-1.5 rounded-lg border border-red-100 font-bold text-sm">
                                            <i class="fas fa-sign-out-alt"></i>
                                            {{ $data->out }}
                                        </div>
                                    @else
                                        <span class="text-gray-300 font-bold">-</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-center whitespace-nowrap">
                                    @if($data->status !== '-')
                                        <span class="px-3 py-1 text-xs font-bold rounded-full 
                                            {{ $data->status === 'hadir' ? 'bg-green-100 text-green-800' : 
                                               ($data->status === 'sakit' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800') }} uppercase tracking-widest">
                                            {{ $data->status }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 font-bold">T/A</span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 text-center whitespace-nowrap font-medium text-gray-600">
                                    {{ $data->distance !== null ? $data->distance . ' km' : '-' }}
                                </td>
                                <td class="px-8 py-6 text-right whitespace-nowrap">
                                    <a href="{{ route('boss.reports.attendance.detail', $data->user->id) }}" 
                                       class="inline-flex items-center justify-center px-4 py-2 bg-white text-blue-600 hover:bg-blue-600 hover:text-white border border-blue-600 rounded-xl font-bold text-xs transition-all shadow-sm active:scale-95 group-hover:shadow-md">
                                       <i class="fas fa-chart-pie mr-2"></i>
                                       Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-20 text-center text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-calendar-times text-6xl mb-4 opacity-10"></i>
                                        <p class="text-xl font-black opacity-50 uppercase tracking-widest">Tidak ada record kehadiran</p>
                                        <p class="text-sm font-bold opacity-30 mt-1">Silakan pilih tanggal lain</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
