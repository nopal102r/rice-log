@extends('layouts.app')

@section('title', 'Persetujuan Absensi Manual')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Persetujuan Absensi Manual</h1>
        <p class="text-gray-600">Review dan setujui permintaan absensi dari karyawan.</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-200">
            <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest mb-1">Menunggu</p>
            <p class="text-3xl font-black text-orange-600">{{ $pendingAttendances->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-200">
            <p class="text-gray-500 text-[10px] font-black uppercase tracking-widest mb-1">Total History</p>
            <p class="text-3xl font-black text-gray-900">{{ $historyAttendances->count() }}</p>
        </div>
    </div>

    <!-- Pending Requests -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="bg-orange-50 px-6 py-4 border-b border-orange-100">
            <h2 class="text-lg font-bold text-orange-800 flex items-center gap-2">
                <i class="fas fa-clock"></i> Permintaan Menunggu Persetujuan
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-gray-500">Karyawan</th>
                        <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-gray-500">Tipe</th>
                        <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-gray-500">Waktu Request</th>
                        <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-gray-500 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pendingAttendances as $attendance)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900">{{ $attendance->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $attendance->user->job_type }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-[10px] font-black uppercase tracking-widest {{ $attendance->type === 'masuk' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ $attendance->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-sm">
                                {{ $attendance->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2">
                                    <button onclick="approveAttendance({{ $attendance->id }})" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm">
                                        <i class="fas fa-check mr-1"></i> Setujui
                                    </button>
                                    <button onclick="rejectAttendance({{ $attendance->id }})" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm">
                                        <i class="fas fa-times mr-1"></i> Tolak
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-check-circle text-4xl mb-3 block text-gray-200"></i>
                                Tidak ada permintaan yang menunggu.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- History -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Riwayat Persetujuan Terakhir</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-gray-500">Karyawan</th>
                        <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-gray-500">Tipe</th>
                        <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-gray-500">Status</th>
                        <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-gray-500">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($historyAttendances as $history)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-700">{{ $history->user->name }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-[10px] font-black uppercase tracking-widest {{ $history->type === 'masuk' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600' }}">
                                    {{ $history->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($history->status_approval === 'approved')
                                    <span class="text-green-600 font-bold text-sm flex items-center gap-1">
                                        <i class="fas fa-check-circle"></i> Disetujui
                                    </span>
                                @else
                                    <span class="text-red-600 font-bold text-sm flex items-center gap-1">
                                        <i class="fas fa-times-circle"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-sm">
                                {{ $history->updated_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script>
    function approveAttendance(id) {
        Swal.fire({
            title: 'Setujui Absensi Manual?',
            text: 'Tindakan ini akan memvalidasi kehadiran karyawan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ url("boss/attendance-approval") }}/' + id + '/approve', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(r => r.json()).then(data => {
                    Swal.fire('Berhasil', data.message, 'success').then(() => location.reload());
                });
            }
        });
    }

    function rejectAttendance(id) {
        Swal.fire({
            title: 'Tolak Absensi Manual?',
            text: 'Karyawan harus melakukan request ulang atau absen normal.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ url("boss/attendance-approval") }}/' + id + '/reject', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(r => r.json()).then(data => {
                    Swal.fire('Ditolak', data.message, 'info').then(() => location.reload());
                });
            }
        });
    }
</script>
@endsection
