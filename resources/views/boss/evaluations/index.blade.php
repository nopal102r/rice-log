@extends('layouts.app')

@section('title', 'Penilaian Karyawan Bulanan')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Penilaian Karyawan</h1>
            <p class="text-gray-600">Periode: <span class="font-bold text-blue-600">{{ \Carbon\Carbon::create(null, $month)->format('F') }} {{ $year }}</span></p>
        </div>
        <a href="{{ route('boss.evaluation-indicators.index') }}" class="text-sm font-bold text-gray-400 hover:text-blue-600 transition-colors flex items-center gap-2">
            <i class="fas fa-cog"></i> Pengaturan Indikator
        </a>
    </div>

    <!-- Progress Summary Card -->
    <div class="bg-white rounded-3xl shadow-xl shadow-blue-50/50 p-8 border border-gray-100 mb-8 overflow-hidden relative">
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex-1">
                    <h2 class="text-xl font-black text-gray-800 uppercase tracking-tight mb-4">Progress Penilaian Bulan Ini</h2>
                    <div class="relative w-full h-10 bg-gray-100 rounded-2xl overflow-hidden border-4 border-gray-50 shadow-inner">
                        <div class="absolute top-0 left-0 h-full bg-gradient-to-r from-blue-600 to-indigo-500 rounded-r-xl transition-all duration-1000 ease-out flex items-center justify-end px-4" style="width: {{ $progress }}%">
                            @if($progress > 10)
                                <span class="text-[10px] font-black text-white uppercase tracking-widest">{{ number_format($progress, 0) }}%</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="bg-blue-50 px-6 py-4 rounded-2xl text-center border border-blue-100 min-w-[120px]">
                        <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest leading-none mb-1">Sudah Dinilai</p>
                        <p class="text-3xl font-black text-blue-700 leading-none">{{ $ratedCount }}</p>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 rounded-2xl text-center border border-gray-100 min-w-[120px]">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Total Karyawan</p>
                        <p class="text-3xl font-black text-gray-900 leading-none">{{ $totalEmployees }}</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Decorative Background Element -->
        <div class="absolute -right-8 -top-8 w-64 h-64 bg-blue-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
    </div>


    <!-- Employee List -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Daftar Karyawan</h3>
            <div class="flex items-center gap-2 text-[10px] font-bold">
                <span class="flex items-center gap-1 text-green-600"><i class="fas fa-circle text-[8px]"></i> Aktif</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left">
                        <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Karyawan</th>
                        <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Jabatan</th>
                        <th class="px-8 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">Status Penilaian</th>
                        <th class="px-8 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($employeeList as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-100 to-indigo-50 flex items-center justify-center text-blue-700 font-black shadow-sm group-hover:scale-110 transition-transform">
                                        {{ substr($item['user']->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-black text-gray-800 leading-tight">{{ $item['user']->name }}</p>
                                        <p class="text-xs text-gray-500">ID: #{{ str_pad($item['user']->id, 3, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest">{{ $item['user']->job }}</span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                @if($item['is_rated'])
                                    <span class="bg-green-100 text-green-700 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest flex inline-flex items-center gap-2">
                                        <i class="fas fa-check-circle"></i> Sudah Dinilai
                                    </span>
                                @else
                                    <span class="bg-orange-100 text-orange-700 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest flex inline-flex items-center gap-2">
                                        <i class="fas fa-clock"></i> Belum Dinilai
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                <a href="{{ route('boss.evaluations.create', $item['user']->id) }}" class="inline-flex items-center gap-2 {{ $item['is_rated'] ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-blue-600 text-white hover:bg-blue-700 shadow-blue-100' }} px-6 py-2.5 rounded-xl font-bold text-sm shadow-md transition-all active:scale-95">
                                    <i class="fas {{ $item['is_rated'] ? 'fa-edit' : 'fa-star' }}"></i>
                                    {{ $item['is_rated'] ? 'Edit Nilai' : 'Beri Nilai' }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
