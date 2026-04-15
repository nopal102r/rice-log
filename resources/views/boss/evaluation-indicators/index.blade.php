@extends('layouts.app')

@section('title', 'Manajemen Indikator Penilaian')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Konfigurasi Penilaian</h1>
            <p class="text-gray-500 font-medium">Atur kriteria dan indikator performa karyawan Anda secara dinamis.</p>
        </div>
        <a href="{{ route('boss.evaluation-indicators.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-7 py-3.5 rounded-2xl font-bold shadow-lg shadow-blue-100 transition-all active:scale-95 flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Indikator Baru
        </a>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @forelse($indicators as $indicator)
            <div class="bg-white rounded-2xl shadow-md border border-gray-200 overflow-hidden group hover:shadow-lg transition-all duration-300">
                <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800 uppercase tracking-tight">{{ $indicator->name }}</h2>
                    <div class="flex gap-1">
                        <a href="{{ route('boss.evaluation-indicators.edit', $indicator->id) }}" class="text-gray-400 hover:text-blue-600 p-2 rounded-xl hover:bg-blue-50 transition-all">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('boss.evaluation-indicators.destroy', $indicator->id) }}" method="POST" onsubmit="return confirm('Hapus indikator ini dan semua deskripsinya?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="p-6">
                    <div class="mb-5 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Kriteria / Deskripsi</span>
                        <a href="{{ route('boss.evaluation-indicators.descriptions.create', $indicator->id) }}" class="text-[11px] bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-3 py-1.5 rounded-lg font-bold transition-all border border-blue-100">
                            <i class="fas fa-plus mr-1"></i> Tambah
                        </a>
                    </div>
                    <ul class="space-y-2.5">
                        @forelse($indicator->descriptions as $desc)
                            <li class="flex justify-between items-center bg-gray-50/50 p-4 rounded-xl border border-gray-100 hover:border-gray-300 transition-all group/item">
                                <span class="text-gray-700 font-medium text-sm">{{ $desc->name }}</span>
                                <div class="flex gap-1 opacity-0 group-hover/item:opacity-100 transition-opacity">
                                    <a href="{{ route('boss.evaluation-descriptions.edit', $desc->id) }}" class="text-gray-400 hover:text-blue-500 p-1.5 rounded-lg hover:bg-white transition-all">
                                        <i class="fas fa-pencil-alt text-[10px]"></i>
                                    </a>
                                    <form action="{{ route('boss.evaluation-descriptions.destroy', $desc->id) }}" method="POST" onsubmit="return confirm('Hapus item penilaian ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-1">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @empty
                            <li class="text-center py-4 text-gray-400 text-sm italic border-2 border-dashed border-gray-100 rounded-xl">
                                Belum ada deskripsi. Klik "Tambah Item" untuk memulai.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @empty
            <div class="lg:col-span-2 bg-white rounded-3xl p-16 text-center border-2 border-dashed border-gray-200">
                <div class="w-24 h-24 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Belum ada Indikator Penilaian</h3>
                <p class="text-gray-500 mb-8 max-w-sm mx-auto">Buat indikator seperti "Disiplin", "Teamwork", atau "Kinerja" untuk mulai menilai karyawan Anda.</p>
                <a href="{{ route('boss.evaluation-indicators.create') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-2xl font-black shadow-xl shadow-blue-100 transition-all active:scale-95">
                    Buat Indikator Pertama
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
