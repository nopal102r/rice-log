@extends('layouts.app')

@section('title', 'Form Penilaian Karyawan')

@section('extra-css')
<style>
    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        gap: 0.5rem;
    }
    .star-rating input {
        display: none;
    }
    .star-rating label {
        cursor: pointer;
        width: 40px;
        height: 40px;
        background-color: #f3f4f6;
        display: flex;
        items-center: center;
        justify-content: center;
        border-radius: 12px;
        color: #d1d5db;
        transition: all 0.2s;
    }
    .star-rating label i {
        font-size: 1.2rem;
    }
    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        background-color: #fef3c7;
        color: #f59e0b;
        transform: scale(1.1);
    }
    .star-rating input:checked + label {
        background-color: #f59e0b;
        color: white;
    }
    
    .indicator-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .indicator-card:focus-within {
        border-color: #3b82f6;
        box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.1);
    }
</style>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-10 flex items-center justify-between">
        <div class="flex items-center gap-6">
            <a href="{{ route('boss.evaluations.index') }}" class="w-12 h-12 bg-white border border-gray-200 rounded-2xl flex items-center justify-center text-gray-400 hover:text-blue-600 hover:border-blue-100 hover:shadow-lg transition-all active:scale-90">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black text-gray-800 leading-none mb-2">Beri Penilaian</h1>
                <p class="text-sm text-gray-500 uppercase tracking-widest font-bold">Periode {{ \Carbon\Carbon::create(null, $month)->format('F') }} {{ $year }}</p>
            </div>
        </div>
        
        <div class="bg-blue-600 px-8 py-4 rounded-3xl shadow-xl shadow-blue-100 text-white flex items-center gap-4 border-4 border-blue-500/20">
            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-xl font-black">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div>
                <p class="font-black leading-tight">{{ $user->name }}</p>
                <p class="text-[10px] uppercase font-black tracking-widest text-blue-100 opacity-80">{{ $user->job }}</p>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-8 rounded-r-xl shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('boss.evaluations.store') }}" method="POST" id="evaluationForm">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year" value="{{ $year }}">
        <input type="hidden" name="next" id="nextFlag" value="0">

        <div class="space-y-12">
            @foreach($indicators as $indicator)
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden indicator-card">
                    <div class="bg-gray-50/50 px-8 py-5 border-b border-gray-100 flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                        <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">{{ $indicator->name }}</h2>
                    </div>
                    <div class="p-8 space-y-8">
                        @foreach($indicator->descriptions as $desc)
                            @php
                                $existingRating = $existingEvaluation ? $existingEvaluation->ratings->where('evaluation_description_id', $desc->id)->first() : null;
                                $selectedValue = $existingRating ? $existingRating->rating : 0;
                            @endphp
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-8 border-b border-gray-50 last:border-0 last:pb-0">
                                <div class="max-w-md">
                                    <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $desc->name }}</h3>
                                    <p class="text-sm text-gray-400">Berikan penilaian 1 sampai 5 bintang.</p>
                                </div>
                                <div class="star-rating">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" id="rating-{{ $desc->id }}-{{ $i }}" name="ratings[{{ $desc->id }}]" value="{{ $i }}" {{ $selectedValue == $i ? 'checked' : '' }} required>
                                        <label for="rating-{{ $desc->id }}-{{ $i }}" title="{{ $i }} Bintang">
                                            <i class="fas fa-star"></i>
                                        </label>
                                    @endfor
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Feedback Card -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gray-50/50 px-8 py-5 border-b border-gray-100 flex items-center gap-3">
                    <i class="fas fa-comment-dots text-gray-400"></i>
                    <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">Catatan & Feedback</h2>
                </div>
                <div class="p-8">
                    <textarea name="feedback" rows="4" class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-6 py-4 outline-none focus:border-blue-500 transition-all font-medium text-gray-800" placeholder="Berikan komentar atau motivasi untuk karyawan ini...">{{ $existingEvaluation ? $existingEvaluation->feedback : '' }}</textarea>
                </div>
            </div>

            <!-- Bonus Card -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                <div class="bg-gray-50/50 px-8 py-5 border-b border-gray-100 flex items-center gap-3">
                    <i class="fas fa-gift text-blue-500"></i>
                    <h2 class="text-sm font-black text-gray-800 uppercase tracking-widest">Bonus Tambahan (Opsional)</h2>
                </div>
                <div class="p-8">
                    <div class="relative">
                        <span class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 font-black">Rp</span>
                        <input type="number" name="bonus" value="{{ $existingEvaluation ? $existingEvaluation->bonus : 0 }}" 
                            class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl pl-16 pr-6 py-4 outline-none focus:border-blue-500 transition-all font-black text-gray-800 text-xl" 
                            placeholder="0">
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2 font-bold uppercase tracking-widest pl-2">Bonus ini akan otomatis ditambahkan ke total gaji bulan {{ \Carbon\Carbon::create(null, $month)->format('F') }}.</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col md:flex-row gap-4 pb-20">
                <button type="submit" onclick="setNext(0)" class="flex-1 bg-white border-2 border-gray-200 hover:border-gray-800 hover:bg-gray-800 hover:text-white text-gray-800 py-5 rounded-3xl font-black transition-all active:scale-95 shadow-lg shadow-gray-100">
                    <i class="fas fa-save mr-2"></i> Simpan Penilaian
                </button>
                <button type="submit" onclick="setNext(1)" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-5 rounded-3xl font-black transition-all active:scale-95 shadow-xl shadow-blue-100 group">
                    Simpan & Lanjut <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('extra-js')
<script>
    function setNext(val) {
        document.getElementById('nextFlag').value = val;
    }

    // Auto-scroll to next group after rating
    document.querySelectorAll('.star-rating input').forEach(input => {
        input.addEventListener('change', function() {
            const nextGroup = this.closest('.star-rating').closest('div').nextElementSibling;
            if (nextGroup && nextGroup.classList.contains('border-b')) {
                // Just a subtle feedback could be nice, but maybe scrolling is too much.
            }
        });
    });
</script>
@endsection
