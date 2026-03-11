@extends('layouts.app')

@section('title', 'Penilaian Karyawan Bulanan')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 mb-1">Penilaian Karyawan</h1>
            <p class="text-gray-500 text-sm">Periode: <span class="font-bold text-blue-600">{{ \Carbon\Carbon::create(null, $month)->format('F') }} {{ $year }}</span></p>
        </div>
        <a href="{{ route('boss.evaluation-indicators.index') }}" class="text-sm font-medium text-gray-500 hover:text-blue-600 transition-colors flex items-center gap-2">
            <i class="fas fa-cog"></i> Pengaturan Indikator
        </a>
    </div>

    <!-- Progress Summary Card -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-200 mb-8 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex-1">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-bold text-gray-600 uppercase tracking-wider">Progress Penilaian</h2>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-lg border border-blue-100">{{ $ratedCount }} / {{ $totalEmployees }} Terisi</span>
                </div>
                <div class="relative w-full h-3 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
                    <div class="absolute top-0 left-0 h-full bg-blue-600 rounded-full transition-all duration-1000 ease-out shadow-[0_0_10px_rgba(59,130,246,0.5)]" style="width: {{ $progress }}%"></div>
                </div>
            </div>
            <div class="flex gap-3">
                <div class="bg-blue-50/80 px-5 py-3 rounded-xl border border-blue-200">
                    <p class="text-[11px] font-medium text-blue-500 uppercase tracking-wide leading-none mb-2">Sudah Dinilai</p>
                    <p class="text-2xl font-black text-blue-800 leading-none">{{ $ratedCount }}</p>
                </div>
                <div class="bg-gray-50/80 px-5 py-3 rounded-xl border border-gray-200">
                    <p class="text-[11px] font-medium text-gray-500 uppercase tracking-wide leading-none mb-2">Total Karyawan</p>
                    <p class="text-2xl font-black text-gray-900 leading-none">{{ $totalEmployees }}</p>
                </div>
            </div>
        </div>
    </div>


    <!-- Employee List -->
    <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden">
        <div class="px-8 py-5 border-b border-gray-200 flex justify-between items-center bg-gray-50/30">
            <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider">Daftar Karyawan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left bg-gray-50/50">
                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-200">Karyawan</th>
                        <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-200">Jabatan</th>
                        <th class="px-8 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-200">Status</th>
                        <th class="px-8 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-200">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($employeeList as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-black shadow-md shadow-blue-100 group-hover:scale-105 transition-transform duration-300">
                                        {{ substr($item['user']->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-base font-bold text-gray-900 leading-tight mb-0.5">{{ $item['user']->name }}</p>
                                        <p class="text-xs text-gray-400 font-medium">ID: #{{ str_pad($item['user']->id, 3, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-lg text-xs font-medium uppercase tracking-wider">{{ $item['user']->job }}</span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                @if($item['is_rated'])
                                    <span class="bg-green-50 text-green-600 px-4 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wider border border-green-100 flex inline-flex items-center gap-2">
                                        <i class="fas fa-check-circle"></i> Selesai
                                    </span>
                                @else
                                    <span class="bg-orange-50 text-orange-600 px-4 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wider border border-orange-100 flex inline-flex items-center gap-2">
                                        <i class="fas fa-clock"></i> Tertunda
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                <a href="{{ route('boss.evaluations.create', $item['user']->id) }}" class="inline-flex items-center gap-2 {{ $item['is_rated'] ? 'bg-gray-50 text-gray-600 border border-gray-200 hover:bg-gray-100' : 'bg-blue-600 text-white hover:bg-blue-700 shadow-md shadow-blue-100' }} px-5 py-2.5 rounded-xl text-sm font-bold transition-all active:scale-95">
                                    <i class="fas {{ $item['is_rated'] ? 'fa-edit' : 'fa-star' }}"></i>
                                    {{ $item['is_rated'] ? 'Edit' : 'Nilai' }}
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
