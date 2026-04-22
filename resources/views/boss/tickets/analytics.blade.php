@extends('layouts.app')

@section('title', 'Analitik Aduan')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Analitik & Performa</h1>
        <p class="text-gray-500 mt-1">Pantau efisiensi operator dan kepuasan karyawan.</p>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Aduan</p>
            <h2 class="text-3xl font-black text-gray-800">{{ $statusSummary->sum('count') }}</h2>
        </div>
        <div class="bg-blue-600 p-6 rounded-2xl shadow-lg text-white">
            <p class="text-xs font-bold opacity-70 uppercase tracking-widest mb-1">Skor CSAT (Kepuasan)</p>
            <div class="flex items-center gap-2">
                <h2 class="text-3xl font-black">{{ number_format($csat, 1) }}</h2>
                <div class="text-yellow-400 flex text-sm">
                    @for($i=1; $i<=5; $i++)
                        <i class="fa{{ $i <= round($csat) ? 's' : 'r' }} fa-star"></i>
                    @endfor
                </div>
            </div>
            <p class="text-[10px] mt-2 opacity-70">Rata-rata dari {{ $statusSummary->where('status', 'closed')->first()->count ?? 0 }} penilaian selesai.</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Rata-rata Respon</p>
            <h2 class="text-3xl font-black text-gray-800">{{ round($operatorPerformance->avg('avg_response'), 1) }} <span class="text-sm font-normal text-gray-400">Menit</span></h2>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Selesai/Resolved</p>
            <h2 class="text-3xl font-black text-green-600">{{ $statusSummary->whereIn('status', ['resolved', 'closed'])->sum('count') }}</h2>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        <!-- Operator Performance Table -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Performa Operator</h3>
                    <span class="text-xs text-gray-400">Real-time Efficiency</span>
                </div>
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 text-[10px] font-bold text-gray-400 uppercase">
                            <th class="px-6 py-3">Petugas</th>
                            <th class="px-6 py-3 text-center">Tiket</th>
                            <th class="px-6 py-3 text-center">Avg Respon</th>
                            <th class="px-6 py-3 text-center">Avg Selesai</th>
                            <th class="px-6 py-3 text-right">CSAT</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($operatorPerformance as $op)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-sm text-gray-700">{{ $op['name'] }}</td>
                                <td class="px-6 py-4 text-center text-sm font-mono">{{ $op['count'] }}</td>
                                <td class="px-6 py-4 text-center text-sm">
                                    <span class="px-2 py-0.5 rounded-lg {{ $op['avg_response'] < 30 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $op['avg_response'] }}m
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $op['avg_resolution'] }}m</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1 font-bold text-blue-600">
                                        <i class="fas fa-star text-yellow-400 text-xs"></i>
                                        {{ $op['avg_rating'] }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Trends Section -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-6">Tren Kendala Terpopuler</h3>
                <div class="space-y-6">
                    @foreach($trends as $trend)
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-bold text-gray-600">{{ $trend->name }}</span>
                                <span class="text-xs text-gray-400 font-mono">{{ $trend->count }} Lap.</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-blue-600 h-full rounded-full" style="width: {{ ($trend->count / max($trends->sum('count'), 1)) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Status Distribution -->
            <div class="bg-gray-900 rounded-2xl shadow-sm p-6 text-white">
                <h3 class="font-bold text-sm mb-4 opacity-80 uppercase tracking-widest">Status Distribution</h3>
                <div class="space-y-3">
                    @foreach($statusSummary as $ss)
                        <div class="flex justify-between items-center">
                            <span class="text-xs opacity-70 capitalize">{{ $ss->status }}</span>
                            <span class="text-xs font-bold px-2 py-0.5 bg-white/10 rounded">{{ $ss->count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
