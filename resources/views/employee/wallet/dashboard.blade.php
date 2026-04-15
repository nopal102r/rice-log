@extends('layouts.app')

@section('title', 'Dompet Integritas')

@section('content')
<div class="max-w-4xl mx-auto pb-12">
    <!-- Hero / Wallet Card -->
    <div class="bg-gradient-to-br from-blue-700 to-indigo-900 rounded-3xl p-8 text-white shadow-xl mb-8 relative overflow-hidden">
        <!-- Decor elements -->
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute right-20 -bottom-10 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center relative z-10">
            <div>
                <p class="text-blue-200 font-bold tracking-widest text-sm mb-1 uppercase">Saldo Poin Integritas</p>
                <div class="flex items-end gap-3">
                    <h1 class="text-5xl md:text-7xl font-black">{{ $user->current_points }}</h1>
                    <span class="text-blue-200 font-bold mb-2 md:mb-3">PTS</span>
                </div>
            </div>
            <div class="mt-6 md:mt-0 bg-white/20 backdrop-blur-md border border-white/20 rounded-2xl p-4 flex items-center gap-4 shadow-lg shadow-black/10">
                <div class="w-12 h-12 rounded-full bg-yellow-400 flex items-center justify-center text-yellow-900 text-xl shadow-inner border border-yellow-200">
                    <i class="fas fa-crown"></i>
                </div>
                <div>
                    <p class="text-[10px] text-blue-200 uppercase font-black tracking-wider">Level Pengguna</p>
                    <p class="text-xl font-bold tracking-wider">{{ $level }}</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-xl mb-8 shadow-sm">
        <p class="font-bold">Berhasil</p>
        <p>{{ session('success') }}</p>
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-8 shadow-sm">
        <p class="font-bold">Gagal</p>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <div class="mb-8 border-b border-gray-200">
        <nav class="flex gap-2 overflow-x-auto" aria-label="Tabs">
            <button onclick="switchTab('marketplace')" id="tab-marketplace" class="shrink-0 border-b-2 border-blue-600 px-4 pb-4 text-sm font-bold text-blue-600 transition">Marketplace</button>
            <button onclick="switchTab('inventory')" id="tab-inventory" class="shrink-0 border-b-2 border-transparent px-4 pb-4 text-sm font-bold text-gray-500 hover:border-gray-300 hover:text-gray-700 transition">Inventory Token</button>
            <button onclick="switchTab('history')" id="tab-history" class="shrink-0 border-b-2 border-transparent px-4 pb-4 text-sm font-bold text-gray-500 hover:border-gray-300 hover:text-gray-700 transition">Riwayat Mutasi</button>
        </nav>
    </div>

    <!-- Marketplace Section -->
    <div id="content-marketplace" class="block animate-fade-in">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Marketplace Khusus</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($items as $item)
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-200 flex flex-col justify-between hover:shadow-md transition">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-14 h-14 bg-indigo-50 border border-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 text-2xl shadow-inner">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <span class="bg-orange-100 text-orange-800 font-black px-3 py-1 rounded-xl shadow-sm border border-orange-200 border-b-4">⭐ {{ $item->point_cost }} PTS</span>
                    </div>
                    <h3 class="font-bold text-gray-800 text-lg">{{ $item->item_name }}</h3>
                    <p class="text-[11px] font-black uppercase mt-2 tracking-wider {{ $item->stock_left === 'unlimited' ? 'text-green-600' : ($item->stock_left > 0 ? 'text-blue-600' : 'text-red-500') }}">
                        <i class="fas fa-box-open mr-1"></i> Stok Tersedia: {{ $item->stock_left === 'unlimited' ? '∞ Tak Terbatas' : $item->stock_left }}
                    </p>
                </div>
                <form action="{{ route('employee.wallet.purchase', $item->id) }}" method="POST" class="mt-8" onsubmit="return confirm('Tukar {{ $item->point_cost }} poin dengan token ini?')">
                    @csrf
                    @php $canBuy = $user->current_points >= $item->point_cost && $item->stock_left !== 0; @endphp
                    <button type="submit" @if(!$canBuy) disabled @endif 
                        class="w-full py-4 rounded-xl font-bold transition {{ $canBuy ? 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg shadow-indigo-200 border-b-4 border-indigo-800 active:border-b-0 active:translate-y-1' : 'bg-gray-100 text-gray-400 border border-gray-200 cursor-not-allowed' }}">
                        @if($item->stock_left === 0)
                            Stok Habis Terjual
                        @elseif($user->current_points < $item->point_cost)
                            Poin Tidak Cukup
                        @else
                            Tukar Poin
                        @endif
                    </button>
                </form>
            </div>
            @empty
            <div class="col-span-2 text-center py-12 text-gray-500 bg-gray-50 rounded-3xl border border-dashed border-gray-300">
                Belum ada item di marketplace.
            </div>
            @endforelse
        </div>
    </div>

    <!-- Inventory Section -->
    <div id="content-inventory" class="hidden animate-fade-in">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Token Anda</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($inventory as $token)
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-200 flex items-center gap-4 relative overflow-hidden hover:shadow transition">
                <div class="w-12 h-12 shrink-0 bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100 text-blue-600 rounded-xl flex items-center justify-center text-xl shadow-inner">
                    <i class="fas fa-gem"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-gray-800">{{ $token->item->item_name }}</h4>
                    <p class="text-xs text-gray-500 font-bold mt-1 uppercase tracking-widest">{{ $token->created_at->format('d M y') }}</p>
                </div>
                <div>
                    @if($token->status === 'AVAILABLE')
                        <span class="bg-green-100 border border-green-200 text-green-700 text-[10px] font-black px-2 py-1 rounded">TERSEDIA</span>
                    @elseif($token->status === 'USED')
                        <span class="bg-gray-100 border border-gray-200 text-gray-500 text-[10px] font-black px-2 py-1 rounded">TERPAKAI</span>
                    @else
                        <span class="bg-red-100 border border-red-200 text-red-600 text-[10px] font-black px-2 py-1 rounded">EXPIRED</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-span-2 text-center py-12 text-gray-500 bg-gray-50 rounded-3xl border border-dashed border-gray-300 font-bold">
                <i class="fas fa-box-open text-3xl mb-3 text-gray-300 block"></i>
                Anda belum memiliki token.
            </div>
            @endforelse
        </div>
    </div>

    <!-- History Section -->
    <div id="content-history" class="hidden animate-fade-in">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Riwayat Poin</h2>
        <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden divide-y divide-gray-100">
            @forelse($history as $tx)
            <div class="p-4 md:p-6 flex items-center justify-between hover:bg-gray-50 transition border-l-4 {{ $tx->amount > 0 ? 'border-l-green-500' : 'border-l-red-500' }}">
                <div class="flex items-center gap-4">
                    @if($tx->amount > 0)
                    <div class="w-10 h-10 rounded-full bg-green-50 text-green-600 flex items-center justify-center shrink-0 border border-green-100 shadow-sm">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                    @else
                    <div class="w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center shrink-0 border border-red-100 shadow-sm">
                        <i class="fas fa-arrow-up"></i>
                    </div>
                    @endif
                    
                    <div>
                        <p class="font-bold text-gray-800">{{ $tx->description }}</p>
                        <p class="text-xs text-gray-500 mt-1 font-bold">{{ $tx->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-black text-xl {{ $tx->amount > 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $tx->amount > 0 ? '+' : '' }}{{ $tx->amount }}
                    </p>
                    <p class="text-[10px] uppercase font-bold text-gray-400">Saldo: {{ $tx->current_balance }}</p>
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-gray-500 font-bold bg-gray-50">
                Belum ada pergerakan poin.
            </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease forwards;
    }
</style>
@endsection

@section('extra-js')
<script>
    function switchTab(tab) {
        ['marketplace', 'inventory', 'history'].forEach(t => {
            const btn = document.getElementById('tab-' + t);
            const content = document.getElementById('content-' + t);
            
            btn.classList.remove('border-blue-600', 'text-blue-600');
            btn.classList.add('border-transparent', 'text-gray-500');
            
            content.classList.add('hidden');
            content.classList.remove('block');
        });

        const actBtn = document.getElementById('tab-' + tab);
        const actContent = document.getElementById('content-' + tab);
        
        actBtn.classList.add('border-blue-600', 'text-blue-600');
        actBtn.classList.remove('border-transparent', 'text-gray-500');
        
        actContent.classList.remove('hidden');
        actContent.classList.add('block');
    }
</script>
@endsection
