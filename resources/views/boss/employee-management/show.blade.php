@extends('layouts.app')

@section('title', 'Detail Karyawan - ' . $employee->name)

@section('content')
    <div class="max-w-6xl">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-start">
            <div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">{{ $employee->name }}</h1>
                <p class="text-gray-600">
                    <i class="fas fa-birthday-cake mr-2"></i> Umur: <strong>{{ $employee->getAge() }} tahun</strong> |
                    <i class="fas fa-envelope mr-2"></i> {{ $employee->email }} |
                    <i class="fas fa-phone mr-2"></i> {{ $employee->phone }}
                </p>
            </div>
            <span
                class="px-4 py-2 rounded font-bold text-white {{ $employee->status === 'active' ? 'bg-green-500' : 'bg-red-500' }}">
                {{ $employee->status === 'active' ? 'AKTIF' : 'TIDAK AKTIF' }}
            </span>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <p class="text-gray-500 text-sm mb-2">Status Bulan Ini</p>
                <p class="text-2xl font-bold {{ $summary->status === 'active' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $summary->status === 'active' ? 'AKTIF' : 'TIDAK AKTIF' }}
                </p>
            </div>

            <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <p class="text-gray-500 text-sm mb-2">Hari Hadir</p>
                <p class="text-3xl font-bold text-green-600">{{ $summary->days_present }}</p>
            </div>

            <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                <p class="text-gray-500 text-sm mb-2">Total Kg Setor</p>
                <p class="text-3xl font-bold text-orange-600">{{ $summary->total_kg_deposited }}</p>
            </div>

            <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <p class="text-gray-500 text-sm mb-2">Gaji Bulan Ini</p>
                <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($summary->total_salary, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Personal Info -->
            <div class="card-hover bg-white rounded-lg shadow p-6 col-span-1">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-blue-600"></i> Informasi Pribadi
                </h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-gray-600 text-sm">Nama</p>
                        <p class="font-bold text-gray-800">{{ $employee->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Posisi</p>
                        <p class="font-bold text-gray-800 capitalize">{{ str_replace('_', ' ', $employee->job) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Email</p>
                        <p class="font-bold text-gray-800">{{ $employee->email }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Telepon</p>
                        <p class="font-bold text-gray-800">{{ $employee->phone }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Tanggal Lahir</p>
                        <p class="font-bold text-gray-800">{{ $employee->date_of_birth->format('d-m-Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Alamat</p>
                        <p class="font-bold text-gray-800">{{ $employee->address ?? 'Tidak ada' }}</p>
                    </div>
                </div>
            </div>

            <!-- Monthly Stats -->
            <div class="card-hover bg-white rounded-lg shadow p-6 col-span-2">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-bar text-green-600"></i> Statistik Bulan Ini
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded">
                        <p class="text-gray-600 text-sm mb-1">Hadir</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $summary->days_present }}</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded">
                        <p class="text-gray-600 text-sm mb-1">Sakit</p>
                        <p class="text-3xl font-bold text-red-600">{{ $summary->days_sick }}</p>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded">
                        <p class="text-gray-600 text-sm mb-1">Izin</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $summary->days_leave }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded">
                        <p class="text-gray-600 text-sm mb-1">Cuti Disetujui</p>
                        <p class="text-3xl font-bold text-green-600">{{ $summary->leave_approved }}</p>
                    </div>
                    <div class="bg-orange-50 p-4 rounded col-span-2">
                        <p class="text-gray-600 text-sm mb-1">Total Kg Setor</p>
                        <p class="text-2xl font-bold text-orange-600">{{ $summary->total_kg_deposited }} kg</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded col-span-2">
                        <p class="text-gray-600 text-sm mb-1">Total Gaji</p>
                        <p class="text-2xl font-bold text-purple-600">Rp
                            {{ number_format($summary->total_salary, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Absences -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-calendar text-blue-600"></i> Riwayat Absensi Terakhir (10 Hari)
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-700 font-bold">Tanggal</th>
                            <th class="px-4 py-2 text-left text-gray-700 font-bold">Jenis</th>
                            <th class="px-4 py-2 text-left text-gray-700 font-bold">Status</th>
                            <th class="px-4 py-2 text-left text-gray-700 font-bold">Jarak dari Kantor</th>
                            <th class="px-4 py-2 text-left text-gray-700 font-bold">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentAbsences as $absence)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2">{{ $absence->created_at->format('d-m-Y H:i') }}</td>
                                <td class="px-4 py-2">
                                    @if($absence->type === 'masuk')
                                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-bold">Masuk</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-bold">Keluar</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    @if($absence->status === 'hadir')
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold">Hadir</span>
                                    @elseif($absence->status === 'sakit')
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-bold">Sakit</span>
                                    @else
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-bold">Izin</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    @if($absence->distance_from_office > 2)
                                        <span class="text-red-600 font-bold">{{ $absence->distance_from_office }} km ⚠️</span>
                                    @else
                                        <span class="text-green-600 font-bold">{{ $absence->distance_from_office }} km ✓</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-gray-600">{{ $absence->description ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-gray-400">Tidak ada data absensi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Deposits -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-shopping-bag text-orange-600"></i> Riwayat Setor Terakhir (10 Setor)
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-700 font-bold">Tanggal</th>
                            <th class="px-4 py-2 text-left text-gray-700 font-bold">Berat (kg)</th>
                            <th class="px-4 py-2 text-left text-gray-700 font-bold">Harga/kg</th>
                            <th class="px-4 py-2 text-right text-gray-700 font-bold">Total</th>
                            <th class="px-4 py-2 text-center text-gray-700 font-bold">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentDeposits as $deposit)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2">{{ $deposit->created_at->format('d-m-Y H:i') }}</td>
                                <td class="px-4 py-2 font-bold">{{ $deposit->weight }} kg</td>
                                <td class="px-4 py-2">Rp {{ number_format($deposit->price_per_kg, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-right font-bold">Rp
                                    {{ number_format($deposit->total_price, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-center">
                                    @if($deposit->status === 'verified')
                                        <span
                                            class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold">Terverifikasi</span>
                                    @elseif($deposit->status === 'pending')
                                        <span
                                            class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-bold">Menunggu</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-bold">Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-gray-400">Tidak ada data setor</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 flex gap-4">
            <a href="{{ route('boss.employees.index') }}"
                class="inline-block bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>
@endsection