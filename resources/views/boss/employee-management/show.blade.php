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
            <div class="card-hover bg-white rounded-lg shadow p-6 col-span-1 flex flex-col justify-between">
                <div>
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
                    </div>
                </div>
                
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <button onclick="toggleEmployeeStatus({{ $employee->id }})" 
                        class="w-full py-3 rounded-xl font-black transition shadow-md active:scale-95 
                        {{ $employee->status === 'active' ? 'bg-red-50 text-red-600 border-2 border-red-600 hover:bg-red-600 hover:text-white' : 'bg-green-600 text-white hover:bg-green-700' }}">
                        <i class="fas {{ $employee->status === 'active' ? 'fa-user-slash' : 'fa-user-check' }} mr-2"></i>
                        {{ $employee->status === 'active' ? 'Nonaktifkan Karyawan' : 'Aktifkan Karyawan' }}
                    </button>
                </div>
            </div>

            <!-- Monthly Stats & Job Metrics -->
            <div class="card-hover bg-white rounded-lg shadow p-6 col-span-2">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-line text-green-600"></i> Rekap Pelaporan & Kinerja
                </h3>
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                        <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest mb-1">Hadir</p>
                        <p class="text-3xl font-black text-blue-700">{{ $summary->days_present }} <span class="text-sm font-bold text-blue-400">Hari</span></p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-xl border border-purple-100">
                        <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest mb-1">Total Gaji</p>
                        <p class="text-xl font-black text-purple-700">Rp {{ number_format($summary->total_salary, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-orange-50 p-4 rounded-xl border border-orange-100">
                        <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest mb-1">Total Setoran</p>
                        <p class="text-2xl font-black text-orange-700">{{ $jobMetrics['total_kg'] }} <span class="text-sm font-bold text-orange-400">Kg</span></p>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                    <h4 class="text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Detail Setoran ({{ ucfirst($employee->job) }})</h4>
                    
                    @if($employee->job === 'packing')
                        <div class="grid grid-cols-2 gap-4">
                            @forelse($jobMetrics['packing_details'] as $label => $count)
                                <div class="flex justify-between items-center bg-white p-3 rounded-lg shadow-sm border border-gray-100">
                                    <span class="font-bold text-gray-600">{{ $label }}</span>
                                    <span class="bg-blue-600 text-white px-3 py-1 rounded-full font-black text-xs">{{ $count }} Karung</span>
                                </div>
                            @empty
                                <p class="col-span-2 text-center text-gray-400 font-bold py-4 italic">Belum ada data packing bulan ini</p>
                            @endforelse
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between items-center">
                            <span class="font-black text-gray-800">TOTAL SELURUHNYA</span>
                            <span class="text-2xl font-black text-blue-600">{{ $jobMetrics['total_items'] }} <span class="text-sm">Karung</span></span>
                        </div>
                    @elseif($employee->job === 'sales')
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
                            <span class="font-bold text-gray-600">Total Nilai Penjualan</span>
                            <span class="text-2xl font-black text-green-600">Rp {{ number_format($jobMetrics['total_money'], 0, ',', '.') }}</span>
                        </div>
                    @elseif($employee->job === 'ngegiling')
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
                            <span class="font-bold text-gray-600">Total Box/Lainnya</span>
                            <span class="text-2xl font-black text-indigo-600">{{ $jobMetrics['total_items'] }}</span>
                        </div>
                    @elseif($employee->job === 'petani')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col">
                                <span class="text-xs font-black text-gray-400 uppercase mb-2">Total Panen</span>
                                <span class="text-2xl font-black text-green-600">{{ $jobMetrics['total_kg'] }} <span class="text-sm">Kg</span></span>
                            </div>
                            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col">
                                <span class="text-xs font-black text-gray-400 uppercase mb-2">Total Kelola Lahan</span>
                                <span class="text-2xl font-black text-blue-600">{{ $jobMetrics['total_items'] }} <span class="text-sm">Kotak</span></span>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-400 font-bold italic">Data setoran standar (berdasarkan berat kg).</p>
                    @endif
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
                            <th class="px-4 py-2 text-left text-gray-700 font-bold">Hasil / Detail</th>
                            <th class="px-4 py-2 text-left text-gray-700 font-bold">Upah / Harga</th>
                            <th class="px-4 py-2 text-right text-gray-700 font-bold">Total Upah</th>
                            <th class="px-4 py-2 text-center text-gray-700 font-bold">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentDeposits as $deposit)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-2 text-gray-600">{{ $deposit->created_at->format('d-m-Y H:i') }}</td>
                                <td class="px-4 py-2 font-bold">
                                    @if($deposit->type === 'land_management')
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-seedling text-green-600"></i>
                                            <span>{{ $deposit->box_count }} Kotak</span>
                                        </div>
                                        <div class="text-[10px] text-gray-400 font-normal mt-1 flex items-center gap-1">
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
                                        <div class="text-[10px] text-gray-400 font-normal mt-1 uppercase tracking-wider">Setoran Hasil Panen</div>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-gray-700">
                                    @if($deposit->type === 'land_management')
                                        Rp {{ number_format($deposit->price_per_kg, 0, ',', '.') }}<span class="text-[10px] text-gray-400 ml-1">/kotak</span>
                                    @else
                                        Rp {{ number_format($deposit->price_per_kg, 0, ',', '.') }}<span class="text-[10px] text-gray-400 ml-1">/kg</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right font-black text-blue-700">Rp
                                    {{ number_format($deposit->wage_amount ?? $deposit->total_price, 0, ',', '.') }}</td>
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

@section('extra-js')
    <script>
        function toggleEmployeeStatus(employeeId) {
            const isActive = @json($employee->status === 'active');
            const actionText = isActive ? 'menonaktifkan' : 'mengaktifkan';
            
            Swal.fire({
                title: `${actionText.charAt(0).toUpperCase() + actionText.slice(1)} Karyawan?`,
                text: `Apakah Anda yakin ingin ${actionText} karyawan ini?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: isActive ? '#d33' : '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Ya, ${actionText}!`,
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ url("boss/employees") }}/' + employeeId + '/toggle-status', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Berhasil!',
                                `Karyawan telah ${data.status === 'active' ? 'diaktifkan' : 'dinonaktifkan'}.`,
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
                    });
                }
            });
        }
    </script>
@endsection