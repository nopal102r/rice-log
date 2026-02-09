<tr class="hover:bg-gray-50 transition">
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center">
            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full bg-gray-100 text-gray-500">
                @if(str_contains($stock->name, 'gabah'))
                    <i class="fas fa-seedling text-yellow-600"></i>
                @elseif(str_contains($stock->name, 'beras_giling'))
                    <i class="fas fa-mortar-pestle text-gray-600"></i>
                @else
                    <i class="fas fa-box text-blue-500"></i>
                @endif
            </div>
            <div class="ml-4">
                <div class="text-sm font-bold text-gray-800 capitalize">{{ str_replace(['packed_', '_'], ['', ' '], $stock->name) }}</div>
                <div class="text-xs text-gray-500">ID: #{{ str_pad($stock->id, 4, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-center">
        <span class="px-2 py-1 text-xs font-bold rounded-full {{ str_contains($stock->name, 'packed') ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
            {{ str_contains($stock->name, 'packed') ? 'Produk Jadi' : 'Bahan Baku' }}
        </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-black text-gray-700">
        {{ number_format($stock->quantity, 0, ',', '.') }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-center text-xs font-bold text-gray-500 uppercase">
        {{ $stock->unit }}
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
        @if($stock->quantity > 100)
            <span class="text-green-600 font-bold"><i class="fas fa-check-circle mr-1"></i> Aman</span>
        @elseif($stock->quantity > 0)
            <span class="text-orange-500 font-bold"><i class="fas fa-exclamation-triangle mr-1"></i> Menipis</span>
        @else
            <span class="text-red-600 font-bold"><i class="fas fa-times-circle mr-1"></i> Habis</span>
        @endif
    </td>
</tr>
