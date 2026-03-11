@extends('layouts.app')

@section('title', 'Tambah Item Penilaian')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-10 flex items-center gap-6">
        <a href="{{ route('boss.evaluation-indicators.index') }}" class="w-12 h-12 bg-white border border-gray-200 rounded-2xl flex items-center justify-center text-gray-400 hover:text-blue-600 hover:border-blue-100 hover:shadow-lg transition-all active:scale-95">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-extrabold text-blue-900 mb-2">Tambah Item</h1>
            <p class="text-gray-500 font-medium italic">Menambahkan kriteria baru untuk indikator <span class="text-blue-600 font-bold">"{{ $indicator->name }}"</span></p>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-xl shadow-indigo-50/20 border border-indigo-50 overflow-hidden">
        <div class="bg-indigo-600 px-8 py-5 border-b border-indigo-700/10">
             <span class="text-indigo-100 text-xs font-bold uppercase tracking-widest">Kategori Indikator</span>
             <h2 class="text-white text-xl font-black uppercase">{{ $indicator->name }}</h2>
        </div>
        
        <form action="{{ route('boss.evaluation-indicators.descriptions.store', $indicator->id) }}" method="POST" class="p-8 md:p-12">
            @csrf
            
            <div class="mb-8">
                <label for="name" class="block text-gray-400 text-[11px] font-bold uppercase tracking-widest mb-4">Nama Item / Deskripsi Penilaian</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-gray-300 group-focus-within:text-indigo-600 transition-colors">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <input type="text" name="name" id="name" required 
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl pl-14 pr-6 py-5 outline-none focus:border-indigo-600 focus:bg-white transition-all font-bold text-gray-900 text-lg placeholder:text-gray-300" 
                        placeholder="Contoh: Datang tepat waktu, Membantu rekan kerja, dll"
                        value="{{ old('name') }}">
                </div>
                @error('name')
                    <p class="mt-2 text-red-500 text-sm font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col md:flex-row gap-4">
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-5 rounded-2xl font-black shadow-xl shadow-indigo-100 transition-all active:scale-95 flex items-center justify-center gap-3 text-lg">
                    <i class="fas fa-plus-circle"></i>
                    Tambah Item
                </button>
                <a href="{{ route('boss.evaluation-indicators.index') }}" class="flex-1 bg-gray-50 hover:bg-gray-100 text-gray-500 py-5 rounded-2xl font-bold transition-all flex items-center justify-center text-lg">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
