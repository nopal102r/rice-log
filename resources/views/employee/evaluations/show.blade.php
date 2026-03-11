@extends('layouts.app')

@section('title', 'Detail Penilaian Performa')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-10 flex items-center justify-between">
        <div class="flex items-center gap-6">
            <a href="{{ route('employee.evaluations.index') }}" class="w-12 h-12 bg-white border border-gray-200 rounded-2xl flex items-center justify-center text-gray-400 hover:text-blue-600 hover:border-blue-100 hover:shadow-lg transition-all active:scale-90">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black text-gray-800 leading-none mb-2">Detail Penilaian</h1>
                <p class="text-sm text-gray-500 uppercase tracking-widest font-bold">Periode {{ \Carbon\Carbon::create(null, $evaluation->month)->format('F') }} {{ $evaluation->year }}</p>
            </div>
        </div>
        
        <div class="bg-white px-6 py-4 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-4">
            <div class="text-right">
                <p class="text-[10px] uppercase font-black tracking-widest text-gray-400 leading-none mb-1">Skor Rata-rata</p>
                <p class="text-2xl font-black text-blue-600 leading-none">{{ round($evaluation->ratings->avg('rating'), 1) }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                <i class="fas fa-star"></i>
            </div>
        </div>
    </div>

    <div class="space-y-8 pb-20">
        @foreach($groupedRatings as $indicatorName => $ratings)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50/50 px-8 py-5 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                        <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">{{ $indicatorName }}</h2>
                    </div>
                    <span class="text-[10px] font-black text-blue-500 bg-blue-50 px-3 py-1 rounded-full uppercase tracking-widest">
                        Avg: {{ round($ratings->avg('rating'), 1) }}
                    </span>
                </div>
                <div class="p-8 space-y-6">
                    @foreach($ratings as $rating)
                        <div class="flex items-center justify-between gap-6">
                            <div>
                                <h3 class="font-bold text-gray-700">{{ $rating->description->name }}</h3>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="flex gap-1 text-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $rating->rating ? 'text-yellow-400' : 'text-gray-100' }}"></i>
                                    @endfor
                                </div>
                                <span class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-xs font-black text-gray-400 border border-gray-100">
                                    {{ $rating->rating }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Bonus & Feedback Summary -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-8 text-white shadow-xl shadow-blue-100 relative overflow-hidden">
                <h3 class="text-[10px] font-black text-blue-200 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fas fa-gift"></i> Bonus Performa
                </h3>
                <p class="text-4xl font-black mb-1">Rp {{ number_format($evaluation->bonus, 0, ',', '.') }}</p>
                <p class="text-xs text-blue-100 font-medium">Bonus ini telah ditambahkan ke rekapitulasi gaji Anda.</p>
                <!-- Decorative element -->
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 flex flex-col">
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fas fa-quote-left text-blue-500"></i> Feedback Atasan
                </h3>
                <div class="flex-1 bg-gray-50 rounded-2xl p-6 italic text-gray-700 font-medium relative border border-gray-100">
                    @if($evaluation->feedback)
                        "{{ $evaluation->feedback }}"
                    @else
                        <span class="text-gray-300">Tidak ada catatan tambahan dari atasan.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
