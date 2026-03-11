@extends('layouts.app')

@section('title', 'Hasil Penilaian Kinerja')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-10">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Penilaian Kinerja</h1>
        <p class="text-gray-500 font-medium">Lihat ringkasan performa dan feedback dari atasan Anda.</p>
    </div>

    @if(!$latestEvaluation)
        <div class="bg-white rounded-3xl p-16 text-center border-2 border-dashed border-gray-200">
            <div class="w-24 h-24 bg-blue-50 text-blue-300 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl shadow-inner">
                <i class="fas fa-star-half-alt"></i>
            </div>
            <h3 class="text-2xl font-black text-gray-800 mb-2 uppercase tracking-tight">Belum Ada Penilaian</h3>
            <p class="text-gray-500 mb-0 max-w-sm mx-auto">Hasil penilaian kinerja bulanan Anda akan muncul di sini setelah diverifikasi oleh Atasan.</p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <!-- Radar Chart Card -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-md border border-gray-200 p-8 relative overflow-hidden">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex flex-col md:flex-row md:items-center gap-4">
                            <div>
                                <h2 class="text-lg font-bold text-gray-800 tracking-tight leading-none mb-1">Radar Performa</h2>
                                <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest">{{ \Carbon\Carbon::create(null, $latestEvaluation->month)->format('F') }} {{ $latestEvaluation->year }}</p>
                            </div>
                            <a href="{{ route('employee.evaluations.show', $latestEvaluation->id) }}" class="text-[10px] font-bold text-blue-600 bg-blue-50 px-4 py-2 rounded-xl uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all border border-blue-100">
                                <i class="fas fa-search-plus mr-1"></i> Lihat Detail
                            </a>
                        </div>
                        <div class="bg-blue-600 text-white px-4 py-2 rounded-xl text-sm font-black shadow-lg shadow-blue-100 flex items-center gap-2">
                            <i class="fas fa-chart-pie"></i> Visual Insight
                        </div>
                    </div>
                    
                    <div class="relative aspect-square md:aspect-auto md:h-[400px]">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
                <!-- Decorative element -->
                <div class="absolute -left-12 -bottom-12 w-48 h-48 bg-blue-50 rounded-full blur-3xl opacity-50"></div>
            </div>

            <!-- Summary & Feedback Card -->
            <div class="flex flex-col gap-8">
                <!-- Average Score Card -->
                <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-8 text-white shadow-lg shadow-blue-100 relative overflow-hidden">
                    <div class="relative z-10 text-center">
                        <p class="text-[10px] font-bold text-blue-200 uppercase tracking-widest mb-3">Nilai Rata-rata</p>
                        <p class="text-6xl font-black mb-2">{{ round($latestEvaluation->ratings->avg('rating'), 1) }}</p>
                        <div class="flex justify-center gap-1.5 text-yellow-400 mb-5">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= round($latestEvaluation->ratings->avg('rating')) ? '' : 'opacity-30' }}"></i>
                            @endfor
                        </div>
                        
                        @if($latestEvaluation->bonus > 0)
                            <div class="bg-white/10 rounded-2xl py-3 px-5 border border-white/20 backdrop-blur-md mb-2">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-blue-100 mb-1">Bonus Performa</p>
                                <p class="text-xl font-black text-white">Rp {{ number_format($latestEvaluation->bonus, 0, ',', '.') }}</p>
                            </div>
                        @endif
                        <p class="text-xs font-medium text-blue-100">Hebat! Pertahankan performa Anda.</p>
                    </div>
                    <!-- Decorative element -->
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                </div>

                <!-- Feedback Card -->
                <div class="flex-1 bg-white rounded-2xl shadow-md border border-gray-200 p-8 flex flex-col">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i class="fas fa-quote-left text-blue-500"></i> Feedback Atasan
                    </h3>
                    <div class="flex-1 bg-gray-50/50 rounded-2xl p-6 italic text-gray-700 font-medium relative border border-gray-100 text-base leading-relaxed">
                        @if($latestEvaluation->feedback)
                            "{{ $latestEvaluation->feedback }}"
                        @else
                            <span class="text-gray-300">Tidak ada catatan tambahan dari atasan.</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden">
            <div class="px-8 py-5 border-b border-gray-100 bg-gray-50/30 flex justify-between items-center">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Riwayat Penilaian</h3>
                <span class="text-[10px] font-bold text-blue-500 uppercase tracking-widest">Berurut: Terbaru</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-200">Periode</th>
                            <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-200">Skor Performa</th>
                            <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest text-right border-b border-gray-200">Bonus</th>
                            <th class="px-8 py-4 text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-200">Catatan</th>
                            <th class="px-8 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-gray-200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($history as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <p class="font-bold text-gray-900 leading-tight text-base">{{ $item['month'] }}</p>
                                <p class="text-xs text-gray-400 font-medium">{{ $item['year'] }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-2">
                                    <span class="text-lg font-black text-blue-600">{{ $item['average'] }}</span>
                                    <div class="flex gap-0.5 text-[10px] text-yellow-500">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= round($item['average']) ? '' : 'text-gray-200' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <span class="font-bold text-blue-600 text-sm {{ $item['bonus'] > 0 ? '' : 'opacity-20' }}">
                                    {{ $item['bonus'] > 0 ? 'Rp ' . number_format($item['bonus'], 0, ',', '.') : '-' }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-sm text-gray-500 line-clamp-1 max-w-sm italic font-medium">
                                    {{ $item['feedback'] ?: '-' }}
                                </p>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <a href="{{ route('employee.evaluations.show', $item['id']) }}" class="p-2.5 rounded-xl bg-gray-50 text-gray-400 border border-gray-100 hover:text-blue-600 hover:bg-blue-50 hover:border-blue-100 transition-all inline-block">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@section('extra-js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if($chartData)
    const ctx = document.getElementById('performanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'radar',
        data: @json($chartData),
        options: {
            scales: {
                r: {
                    angleLines: {
                        color: 'rgba(200, 200, 200, 0.2)'
                    },
                    grid: {
                        color: 'rgba(200, 200, 200, 0.2)'
                    },
                    suggestedMin: 0,
                    suggestedMax: 5,
                    ticks: {
                        stepSize: 1,
                        backdropColor: 'transparent',
                        color: '#9ca3af',
                        font: {
                            weight: 'bold',
                            size: 10
                        }
                    },
                    pointLabels: {
                        color: '#4b5563',
                        font: {
                            weight: 'black',
                            size: 11,
                            family: 'Inter, sans-serif'
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            maintainAspectRatio: false
        }
    });
    @endif
</script>
@endsection
