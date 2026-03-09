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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                <p class="text-gray-500 text-xs font-black uppercase tracking-widest mb-2">Pencapaian Panen</p>
                <p class="text-2xl font-black text-orange-600">{{ $depositsData['total_kg'] }} <span class="text-sm font-bold">kg</span></p>
            </div>

            @if(auth()->user()->job === 'petani' || auth()->user()->job === 'ngegiling')
            <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500">
                <p class="text-gray-500 text-xs font-black uppercase tracking-widest mb-2">{{ auth()->user()->job === 'petani' ? 'Kelola Lahan' : 'Total Kotak' }}</p>
                <p class="text-2xl font-black text-indigo-600">{{ $depositsData['total_boxes'] }} <span class="text-sm font-bold">{{ auth()->user()->job === 'petani' ? 'Kotak' : 'Pcs' }}</span></p>
            </div>
            @endif

            <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <p class="text-gray-500 text-xs font-black uppercase tracking-widest mb-2">Total Upah</p>
                <p class="text-2xl font-black text-green-600 text-sm">Rp {{ number_format($depositsData['total_wage'], 0, ',', '.') }}</p>
            </div>

            <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <p class="text-gray-500 text-xs font-black uppercase tracking-widest mb-2">Jumlah Laporan</p>
                <p class="text-2xl font-black text-blue-600">{{ $depositsData['count'] }}<span class="text-sm font-bold">x</span></p>
            </div>
        </div>

        <!-- Deposits List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-700 font-bold">Tanggal</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-bold">Jenis / Detail</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-bold">Upah/Harga</th>
                            <th class="px-4 py-3 text-right text-gray-700 font-bold">Total Upah</th>
                            <th class="px-4 py-3 text-center text-gray-700 font-bold">Status</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-bold">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deposits as $deposit)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600 text-xs">{{ $deposit->created_at->format('d-m-Y H:i') }}</td>
                                <td class="px-4 py-3 font-black text-gray-800">
                                    @if($deposit->type === 'land_management')
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-seedling text-green-600"></i>
                                            <span>{{ $deposit->box_count }} Kotak</span>
                                        </div>
                                        <div class="text-[10px] text-gray-400 font-normal flex items-center gap-1 mt-1">
                                            <i class="far fa-clock"></i>
                                            {{ $deposit->start_time ? $deposit->start_time->format('H:i') : '-' }} - 
                                            {{ $deposit->end_time ? $deposit->end_time->format('H:i') : '-' }}
                                            ({{ $deposit->start_time && $deposit->end_time ? $deposit->start_time->diffInHours($deposit->end_time) : 0 }} Jam)
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-weight-hanging text-blue-600"></i>
                                            <span>{{ $deposit->weight }} kg</span>
                                        </div>
                                        <div class="text-[10px] text-gray-400 font-normal mt-1 uppercase tracking-wider">Hasil Panen (Pare)</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-700 text-xs">
                                    @if($deposit->type === 'land_management')
                                        Rp {{ number_format($deposit->price_per_kg, 0, ',', '.') }}<span class="text-[9px] ml-1">/kotak</span>
                                    @else
                                        Rp {{ number_format($deposit->price_per_kg, 0, ',', '.') }}<span class="text-[9px] ml-1">/kg</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-black text-gray-800">
                                    Rp {{ number_format($deposit->wage_amount ?? $deposit->total_price, 0, ',', '.') }}
                                </td>
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