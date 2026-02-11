@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-start">
            <div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">{{ $user->name }}</h1>
                <p class="text-gray-600">
                    <i class="fas fa-birthday-cake mr-2"></i> Umur: <strong>{{ $user->getAge() }} tahun</strong> |
                    <i class="fas fa-envelope mr-2"></i> {{ $user->email }} |
                    <i class="fas fa-phone mr-2"></i> {{ $user->phone }}
                </p>
                <div class="mt-2 flex gap-2">
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-black uppercase tracking-wider">
                        {{ $user->role === 'bos' ? 'BOS / OWNER' : 'KARYAWAN - ' . strtoupper($user->job) }}
                    </span>
                    <span class="px-3 py-1 {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} rounded-full text-xs font-black uppercase tracking-wider">
                        {{ strtoupper($user->status) }}
                    </span>
                </div>
            </div>
            
            @if($user->isEmployee())
                 <div class="bg-blue-600 text-white p-4 rounded-xl shadow-lg transform rotate-2">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-1">Total Gaji Bulan Ini</p>
                    <p class="text-2xl font-black italic">Rp {{ number_format($summary->total_salary, 0, ',', '.') }}</p>
                </div>
            @endif
        </div>

        @if($user->isEmployee())
            <!-- Employee Specific Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex items-center gap-4">
                    <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-calendar-check text-green-600 text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Kehadiran</p>
                        <p class="text-3xl font-black text-gray-800">{{ $summary->days_present }} <span class="text-sm font-bold text-gray-400">Hari</span></p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex items-center gap-4">
                    <div class="w-14 h-14 bg-orange-50 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-weight-hanging text-orange-600 text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Total Setoran</p>
                        <p class="text-3xl font-black text-gray-800">{{ $jobMetrics['total_kg'] }} <span class="text-sm font-bold text-gray-400">Kg</span></p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 flex items-center gap-4">
                    <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-box text-purple-600 text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">{{ $user->job === 'petani' ? 'Lahan Dikelola' : 'Total Karung' }}</p>
                        <p class="text-3xl font-black text-gray-800">{{ $jobMetrics['total_items'] }} <span class="text-sm font-bold text-gray-400">Unit</span></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Data Pribadi -->
                <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100">
                    <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-3">
                        <i class="fas fa-id-card text-blue-600"></i> Data Lengkap
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between border-b border-gray-50 pb-3">
                            <span class="text-gray-400 font-bold text-sm">Alamat</span>
                            <span class="text-gray-800 font-bold">{{ $user->address ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-50 pb-3">
                            <span class="text-gray-400 font-bold text-sm">Tanggal Lahir</span>
                            <span class="text-gray-800 font-bold">{{ $user->date_of_birth ? $user->date_of_birth->format('d F Y') : '-' }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-50 pb-3">
                            <span class="text-gray-400 font-bold text-sm">Mulai Bergabung</span>
                            <span class="text-gray-800 font-bold">{{ $user->created_at->format('d F Y') }}</span>
                        </div>
                         <div class="flex justify-between items-center bg-blue-50/50 p-4 rounded-xl border border-blue-100 mt-4">
                            <div>
                                <span class="text-blue-600 font-black text-xs uppercase block mb-1">Face Recognition</span>
                                <span class="text-gray-800 font-black">{{ $user->hasFaceEnrolled() ? 'SUDAH TERDAFTAR' : 'BELUM TERDAFTAR' }}</span>
                            </div>
                            <i class="fas {{ $user->hasFaceEnrolled() ? 'fa-check-circle text-green-500' : 'fa-times-circle text-red-500' }} text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Job Specific Info -->
                <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100">
                    <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-3">
                        <i class="fas fa-briefcase text-orange-600"></i> Detail Pekerjaan ({{ strtoupper($user->job) }})
                    </h3>
                    
                    @if($user->job === 'packing')
                        <div class="grid grid-cols-1 gap-3">
                            @forelse($jobMetrics['packing_details'] as $label => $count)
                                <div class="flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <span class="font-bold text-gray-600">{{ $label }}</span>
                                    <span class="bg-blue-600 text-white px-4 py-1.5 rounded-full font-black text-xs">{{ $count }} Karung</span>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <i class="fas fa-box-open text-gray-200 text-4xl mb-3 block"></i>
                                    <p class="text-gray-400 font-bold italic">Belum ada data setor karung</p>
                                </div>
                            @endforelse
                        </div>
                    @else
                         <div class="bg-blue-50/50 p-6 rounded-2xl border border-blue-100 text-center">
                            <p class="text-blue-600 font-bold mb-2">Total Setoran Hasil Panen</p>
                            <p class="text-5xl font-black text-gray-800">{{ number_format($jobMetrics['total_kg'], 0) }} <span class="text-xl">Kg</span></p>
                            <p class="text-xs text-gray-400 mt-4 uppercase font-black tracking-widest">Update Otomatis Setiap Setoran Terverifikasi</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent History -->
            <div class="grid grid-cols-1 gap-8">
                <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-black text-gray-800 flex items-center gap-3">
                            <i class="fas fa-history text-purple-600"></i> Riwayat Absensi & Setor
                        </h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-gray-400 text-[10px] font-black uppercase tracking-widest border-b border-gray-50">
                                    <th class="px-4 py-4 text-left">Tanggal</th>
                                    <th class="px-4 py-4 text-left">Aktivitas</th>
                                    <th class="px-4 py-4 text-left">Status/Jarak</th>
                                    <th class="px-4 py-4 text-right">Hasil/Upah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAbsences as $absence)
                                    <tr class="border-b border-gray-50/50 hover:bg-gray-50/50 transition-colors">
                                        <td class="px-4 py-4 text-gray-500 font-bold">{{ $absence->created_at->format('d/m H:i') }}</td>
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-lg {{ $absence->type === 'masuk' ? 'bg-blue-50 text-blue-600' : 'bg-red-50 text-red-600' }} flex items-center justify-center text-[10px]">
                                                    <i class="fas fa-{{ $absence->type === 'masuk' ? 'sign-in-alt' : 'sign-out-alt' }}"></i>
                                                </div>
                                                <span class="font-black text-gray-800 uppercase text-[10px]">Absen {{ ucfirst($absence->type) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="text-xs font-bold {{ $absence->status === 'hadir' ? 'text-green-600' : 'text-orange-600' }}">
                                                {{ ucfirst($absence->status) }} 
                                                <span class="text-gray-300 mx-1">|</span>
                                                <span class="text-gray-500">{{ $absence->distance_from_office }} km</span>
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right font-black text-gray-400">-</td>
                                    </tr>
                                @endforeach

                                @foreach($recentDeposits as $deposit)
                                    <tr class="border-b border-gray-50/50 hover:bg-gray-50/50 transition-colors">
                                        <td class="px-4 py-4 text-gray-500 font-bold">{{ $deposit->created_at->format('d/m H:i') }}</td>
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center text-[10px]">
                                                    <i class="fas fa-weight-hanging"></i>
                                                </div>
                                                <span class="font-black text-gray-800 uppercase text-[10px]">Setoran {{ $deposit->weight }}Kg</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded {{ $deposit->status === 'verified' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                {{ $deposit->status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-right font-black text-blue-600">
                                            @if($deposit->wage_amount)
                                                Rp {{ number_format($deposit->wage_amount, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <!-- Boss Specific Profile View -->
            <div class="bg-white rounded-2xl shadow-sm p-8 border border-gray-100">
                <h3 class="text-xl font-black text-gray-800 mb-6 flex items-center gap-3">
                    <i class="fas fa-user-shield text-blue-600"></i> Informasi Akun Owner
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Nama Lengkap</p>
                            <p class="text-xl font-black text-gray-800">{{ $user->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Email login</p>
                            <p class="text-xl font-black text-gray-800">{{ $user->email }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Nomor HP</p>
                            <p class="text-xl font-black text-gray-800">{{ $user->phone }}</p>
                        </div>
                    </div>
                    <div class="bg-blue-50/50 p-8 rounded-2xl border border-blue-100 flex flex-col justify-center text-center">
                        <i class="fas fa-crown text-blue-600 text-5xl mb-4"></i>
                        <p class="font-black text-blue-800 text-lg uppercase tracking-widest">AKUN ADMINISTRATOR</p>
                        <p class="text-blue-600/70 text-sm mt-2 font-bold italic">Anda memiliki akses penuh untuk mengelola karyawan, payroll, dan laporan sistem.</p>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="mt-8">
            <a href="{{ $user->isBoss() ? route('boss.dashboard') : route('employee.dashboard') }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-gray-600 font-bold transition-colors">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
@endsection
