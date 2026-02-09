@extends('layouts.app')

@section('title', 'Inventori Stok')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Inventori Stok Beras</h1>
            <p class="text-gray-600">Pantau ketersediaan stok gabah, beras giling, dan produk karung secara real-time.</p>
        </div>

        <!-- Stock Inventory Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Raw Materials -->
            @foreach($rawMaterials as $stock)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden border-t-4 {{ $stock->quantity > 0 ? 'border-green-500' : 'border-red-500' }} card-hover">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 rounded-full {{ $stock->quantity > 0 ? 'bg-green-100' : 'bg-red-100' }}">
                                @if(str_contains($stock->name, 'gabah'))
                                    <i class="fas fa-seedling {{ $stock->quantity > 0 ? 'text-green-600' : 'text-red-600' }} text-xl"></i>
                                @else
                                    <i class="fas fa-mortar-pestle {{ $stock->quantity > 0 ? 'text-green-600' : 'text-red-600' }} text-xl"></i>
                                @endif
                            </div>
                        </div>
                        
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">
                            {{ str_replace('_', ' ', $stock->name) }}
                        </h3>
                        <div class="flex items-baseline gap-2">
                            <p class="text-3xl font-bold text-gray-800">{{ number_format($stock->quantity, 0, ',', '.') }}</p>
                            <p class="text-sm font-bold text-gray-500 uppercase">{{ $stock->unit }}</p>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Grouped Packed Products -->
            <div onclick="openPackedModal()" class="bg-white rounded-lg shadow-lg overflow-hidden border-t-4 border-blue-500 card-hover cursor-pointer relative group">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-full bg-blue-100">
                            <i class="fas fa-boxes-stacked text-blue-600 text-xl"></i>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-1 rounded bg-blue-50 text-blue-600 animate-pulse">
                            Klik untuk Detail
                        </span>
                    </div>
                    
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">
                        Total Beras Karung (Packed)
                    </h3>
                    <div class="flex items-baseline gap-2">
                        <p class="text-3xl font-bold text-gray-800">{{ number_format($totalPackedQuantity, 0, ',', '.') }}</p>
                        <p class="text-sm font-bold text-gray-500 uppercase">Karung</p>
                    </div>
                </div>
                <div class="absolute inset-0 bg-blue-600 opacity-0 group-hover:opacity-5 transition-opacity"></div>
            </div>
        </div>


        <!-- Packed Stock Modal -->
        <div id="packedModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" onclick="closePackedModal()">
                    <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-boxes-stacked text-blue-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-bold text-gray-900">Rincian Beras Karung</h3>
                                <p class="text-sm text-gray-500 mb-4">Detail stok perkategori ukuran karung.</p>
                                
                                <div class="grid grid-cols-1 gap-3">
                                    @foreach($packedProducts as $product)
                                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded border border-gray-100">
                                            <div class="flex items-center gap-3">
                                                <i class="fas fa-box text-blue-400"></i>
                                                <span class="font-bold text-gray-700 capitalize">{{ str_replace(['packed_', 'kg'], ['', ' kg'], $product->name) }}</span>
                                            </div>
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-xl font-black text-gray-800">{{ number_format($product->quantity, 0) }}</span>
                                                <span class="text-xs font-bold text-gray-500 uppercase">Karung</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" onclick="closePackedModal()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
<script>
    function openPackedModal() {
        document.getElementById('packedModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closePackedModal() {
        document.getElementById('packedModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close on escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closePackedModal();
    });
</script>
@endsection
