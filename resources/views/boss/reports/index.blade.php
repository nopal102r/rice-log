@extends('layouts.app')

@section('title', 'Laporan Gaji Karyawan')

@section('content')
    <div class="max-w-7xl">
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">Laporan Gaji</h1>
                <p class="text-gray-600">Laporan detail pendapatan dan kinerja karyawan.</p>
            </div>
            
            <!-- Filter -->
            <form method="GET" action="{{ route('boss.reports.index') }}" class="bg-white p-4 rounded-lg shadow-sm flex items-end gap-4">
                <div>
                    <label class="block text-gray-700 font-bold mb-1 text-sm">Bulan</label>
                    <select name="month" class="border border-gray-300 rounded px-3 py-2 w-32 focus:outline-none focus:border-green-500">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-bold mb-1 text-sm">Tahun</label>
                    <select name="year" class="border border-gray-300 rounded px-3 py-2 w-24 focus:outline-none focus:border-green-500">
                        @foreach(range(date('Y') - 1, date('Y') + 1) as $y)
                            <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
            </form>
        </div>

        <!-- Summary Cards for Report Context -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
                <p class="text-gray-500 text-xs font-bold uppercase">Total Gaji (Periode Ini)</p>
                <p class="text-2xl font-bold text-green-700">Rp {{ number_format(collect($reportData)->sum('total_wage'), 0, ',', '.') }}</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow border-l-4 border-orange-500">
                <p class="text-gray-500 text-xs font-bold uppercase">Total Omset Sales</p>
                <p class="text-2xl font-bold text-orange-700">Rp {{ number_format(collect($reportData)->sum('total_revenue'), 0, ',', '.') }}</p>
            </div>
             <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
                <p class="text-gray-500 text-xs font-bold uppercase">Total Beras Masuk/Keluar</p>
                <p class="text-2xl font-bold text-blue-700">{{ number_format(collect($reportData)->sum('total_weight'), 1, ',', '.') }} Kg</p>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Karyawan</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Jabatan</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Jml Setor</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total Berat (Kg)</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Omset Sales (Rp)</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total Gaji (Rp)</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($reportData as $data)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold">
                                                {{ substr($data->user->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $data->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $data->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $data->user->job === 'sales' ? 'bg-purple-100 text-purple-800' : 
                                          ($data->user->job === 'supir' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                        {{ ucfirst($data->user->job) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    {{ $data->deposit_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-700">
                                    {{ number_format($data->total_weight, 1, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-orange-600">
                                    {{ $data->total_revenue > 0 ? 'Rp ' . number_format($data->total_revenue, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-green-600">
                                    Rp {{ number_format($data->total_wage, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('boss.employees.show', $data->user->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                             <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    Tidak ada data untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
