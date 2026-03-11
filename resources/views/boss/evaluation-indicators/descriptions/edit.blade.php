@extends('layouts.app')

@section('title', 'Edit Item Penilaian')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-10 flex items-center gap-6">
        <a href="{{ route('boss.evaluation-indicators.index') }}" class="w-12 h-12 bg-white border border-gray-200 rounded-2xl flex items-center justify-center text-gray-400 hover:text-blue-600 hover:border-blue-100 hover:shadow-lg transition-all active:scale-95">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-3xl font-extrabold text-blue-900 mb-2">Edit Item</h1>
            <p class="text-gray-500 font-medium">Memperbarui kriteria penilaian.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-xl shadow-indigo-50/20 border border-indigo-50 overflow-hidden">
        <div class="bg-indigo-600 px-8 py-5 border-b border-indigo-700/10 text-white flex justify-between items-center">
             <div>
                 <span class="text-indigo-100 text-[10px] font-bold uppercase tracking-widest block mb-1">Indikator</span>
                 <h2 class="text-xl font-black uppercase">{{ $description->indicator->name }}</h2>
             </div>
             <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-2xl">
                 <i class="fas fa-edit"></i>
             </div>
        </div>
        
        <form action="{{ route('boss.evaluation-descriptions.update', $description->id) }}" method="POST" class="p-8 md:p-12">
            @csrf
            @method('PUT')
            
            <div class="mb-8">
                <label for="name" class="block text-gray-400 text-[11px] font-bold uppercase tracking-widest mb-4">Nama Item / Deskripsi Penilaian</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none text-gray-300 group-focus-within:text-indigo-600 transition-colors">
                        <i class="fas fa-list-ul"></i>
                    </div>
                    <input type="text" name="name" id="name" required 
                        class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl pl-14 pr-6 py-5 outline-none focus:border-indigo-600 focus:bg-white transition-all font-bold text-gray-900 text-lg" 
                        placeholder="Masukkan nama item baru"
                        value="{{ old('name', $description->name) }}">
                </div>
                @error('name')
                    <p class="mt-2 text-red-500 text-sm font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col md:flex-row gap-4">
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-5 rounded-2xl font-black shadow-xl shadow-indigo-100 transition-all active:scale-95 flex items-center justify-center gap-3 text-lg">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>
                <a href="{{ route('boss.evaluation-indicators.index') }}" class="flex-1 bg-gray-50 hover:bg-gray-100 text-gray-500 py-5 rounded-2xl font-bold transition-all flex items-center justify-center text-lg">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
