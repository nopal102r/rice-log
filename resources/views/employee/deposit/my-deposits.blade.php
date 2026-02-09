@extends('layouts.app')

@section('title', 'Riwayat Setor Beras')

@section('content')
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Riwayat Setor Beras</h1>
            <p class="text-gray-600">Lihat semua setor beras Anda dan status verifikasinya</p>
        </div>

        <!-- Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                <p class="text-gray-500 text-sm mb-2">Total Kg (Bulan Ini)</p>
                <p class="text-3xl font-bold text-orange-600">{{ $depositsData['total_kg'] }} kg</p>
            </div>

            <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <p class="text-gray-500 text-sm mb-2">Total Penghasilan</p>
                <p class="text-3xl font-bold text-green-600">Rp
                    {{ number_format($depositsData['total_wage'], 0, ',', '.') }}</p>
            </div>

            <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <p class="text-gray-500 text-sm mb-2">Jumlah Setor</p>
                <p class="text-3xl font-bold text-blue-600">{{ $depositsData['count'] }}x</p>
            </div>
        </div>

        <!-- Deposits List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-700 font-bold">Tanggal</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-bold">Berat (kg)</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-bold">Harga/kg</th>
                            <th class="px-4 py-3 text-right text-gray-700 font-bold">Total</th>
                            <th class="px-4 py-3 text-center text-gray-700 font-bold">Status</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-bold">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deposits as $deposit)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-800">{{ $deposit->created_at->format('d-m-Y H:i') }}</td>
                                <td class="px-4 py-3 font-bold text-gray-800">{{ $deposit->weight }} kg</td>
                                <td class="px-4 py-3 text-gray-800">Rp {{ number_format($deposit->price_per_kg, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-gray-800">Rp
                                    {{ number_format($deposit->total_price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="px-2 py-1 rounded text-xs font-bold {{ $deposit->status === 'verified' ? 'bg-green-100 text-green-800' : ($deposit->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $deposit->status === 'verified' ? 'Terverifikasi' : ($deposit->status === 'pending' ? 'Menunggu' : 'Ditolak') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-700 text-sm">
                                    {{ $deposit->notes ? substr($deposit->notes, 0, 30) . '...' : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-2 block opacity-50"></i>
                                    Anda belum melakukan setor beras
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($deposits->hasPages())
                <div class="px-4 py-4 border-t">
                    {{ $deposits->links() }}
                </div>
            @endif
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('employee.deposit.create') }}"
                class="inline-block bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white font-bold py-3 px-6 rounded-lg">
                <i class="fas fa-plus mr-2"></i> Setor Beras Baru
            </a>
        </div>
    </div>
@endsection