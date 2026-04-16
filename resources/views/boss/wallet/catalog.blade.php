@extends('layouts.app')

@section('title', 'Katalog Item Marketplace')

@section('content')
<div class="max-w-7xl mx-auto pb-12">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Marketplace Fleksibilitas</h1>
        <p class="text-gray-600">Kelola item yang dapat dibeli dengan poin oleh karyawan beserta sisa stok.</p>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-8 font-bold">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">📦 Buat Item Baru</h2>
            <form action="{{ route('boss.wallet.storeCatalog') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Token/Item</label>
                    <input type="text" name="item_name" required class="w-full border-gray-300 rounded-xl focus:ring-purple-500 bg-gray-50" placeholder="Cth: Bebas Telat 15 Menit">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Harga Poin</label>
                    <input type="number" name="point_cost" required class="w-full border-gray-300 rounded-xl focus:ring-purple-500 bg-gray-50" placeholder="Cth: 50">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Toleransi Waktu (Menit)</label>
                    <input type="number" name="tolerance_minutes" class="w-full border-gray-300 rounded-xl focus:ring-purple-500 bg-gray-50" placeholder="Kosongkan jika bukan voucher telat. Cth: 30">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Batas Stok (Opsional)</label>
                    <input type="number" name="stock_limit" class="w-full border-gray-300 rounded-xl focus:ring-purple-500 bg-gray-50" placeholder="Kosongi jika tak terbatas">
                </div>
                <button type="submit" class="w-full bg-purple-600 text-white font-black uppercase tracking-wider py-3 rounded-xl hover:bg-purple-700 transition shadow-md shadow-purple-200">
                    Simpan Item
                </button>
            </form>
        </div>

        <!-- Catalog List -->
        <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($items as $item)
            <div class="bg-white rounded-3xl shadow-sm border border-gray-200 flex flex-col justify-between overflow-hidden group">
                <div class="p-6 pb-2">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-14 h-14 bg-purple-50 border border-purple-100 rounded-2xl flex items-center justify-center text-purple-600 text-2xl shadow-inner">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <span class="bg-orange-100 border border-orange-200 text-orange-800 font-black px-3 py-1 rounded-xl shadow-sm">⭐ {{ $item->point_cost }} PTS</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">{{ $item->item_name }}</h3>
                    @if($item->tolerance_minutes)
                        <p class="text-[11px] font-bold text-purple-600 mt-1"><i class="fas fa-clock mr-1"></i>Max Toleransi: {{ $item->tolerance_minutes }} Menit</p>
                    @endif
                    
                    <p class="text-[11px] font-black uppercase mt-2 tracking-wider {{ $item->stock_left === 'unlimited' ? 'text-green-600' : ($item->stock_left > 0 ? 'text-blue-600' : 'text-red-500') }}">
                        <i class="fas fa-box-open mr-1"></i> Stok 
                        @if($item->stock_left === 'unlimited')
                            Tak Terbatas
                        @else
                            Sisa: {{ $item->stock_left }} / {{ $item->stock_limit }}
                        @endif
                    </p>
                </div>
                
                <!-- Pembeli List (Scrollable) -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex-1">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Daftar Penukar</h4>
                    @if($item->userTokens->count() > 0)
                        <div class="space-y-2 max-h-32 overflow-y-auto pr-2 custom-scrollbar">
                            @foreach($item->userTokens as $token)
                                <div class="flex justify-between items-center text-xs bg-white p-2 rounded-lg border border-gray-200 shadow-sm">
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                            {{ substr($token->user->name, 0, 1) }}
                                        </div>
                                        <span class="font-bold text-gray-700 truncate w-24" title="{{ $token->user->name }}">{{ $token->user->name }}</span>
                                    </div>
                                    <span class="text-gray-400 font-bold uppercase" title="{{ $token->created_at->format('d M Y, H:i') }}">
                                        {{ $token->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-400 italic">Belum ada Karyawan yang menukar.</p>
                    @endif
                </div>

                <div class="p-6 pt-4 border-t border-gray-100 bg-white">
                    <form action="{{ route('boss.wallet.destroyCatalog', $item->id) }}" method="POST" onsubmit="return confirm('Hapus item ini sepenuhnya?')">
                        @csrf
                        @method('DELETE')
                        <button class="w-full py-2 bg-red-50 border border-red-100 text-red-600 hover:bg-red-600 hover:text-white rounded-xl font-bold transition">
                            <i class="fas fa-trash mr-1"></i> Hapus Katalog
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="col-span-2 bg-gray-50 border border-dashed border-gray-300 rounded-3xl flex flex-col items-center justify-center py-16 text-gray-400">
                <i class="fas fa-store-alt text-5xl mb-4 text-gray-300"></i>
                <p class="font-bold">Katalog masih kosong.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<style>
/* Custom Scrollbar for buyers list */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1; 
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1; 
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8; 
}
</style>
@endsection
