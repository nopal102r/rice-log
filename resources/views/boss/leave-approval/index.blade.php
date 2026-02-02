@extends('layouts.app')

@section('title', 'Persetujuan Pengajuan Cuti')

@section('content')
    <div class="max-w-6xl">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Persetujuan Pengajuan Cuti</h1>
            <p class="text-gray-600">Lihat dan approve/reject pengajuan cuti dari karyawan</p>
        </div>

        <!-- Tabs -->
        <div class="flex gap-4 mb-8 border-b border-gray-200">
            <button class="tab-btn px-4 py-3 font-bold border-b-2 border-blue-600 text-blue-600" data-tab="pending">
                <i class="fas fa-hourglass-half mr-2"></i> Menunggu ({{ $submissions->count() }})
            </button>
            <button class="tab-btn px-4 py-3 font-bold text-gray-600 hover:text-gray-800" data-tab="approved">
                <i class="fas fa-check-circle mr-2"></i> Disetujui ({{ $approved->count() }})
            </button>
            <button class="tab-btn px-4 py-3 font-bold text-gray-600 hover:text-gray-800" data-tab="rejected">
                <i class="fas fa-times-circle mr-2"></i> Ditolak ({{ $rejected->count() }})
            </button>
        </div>

        <!-- Pending Submissions -->
        <div id="pending-content" class="tab-content">
            <div class="space-y-4">
                @forelse($submissions as $submission)
                    <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-yellow-400">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                            <div>
                                <p class="text-gray-600 text-sm font-bold">Karyawan</p>
                                <p class="text-lg font-bold text-gray-800">{{ $submission->user->name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm font-bold">Tanggal Cuti</p>
                                <p class="text-lg font-bold text-gray-800">{{ $submission->start_date->format('d-m-Y') }} s/d
                                    {{ $submission->end_date->format('d-m-Y') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm font-bold">Total Hari</p>
                                <p class="text-2xl font-bold text-yellow-600">{{ $submission->getTotalDays() }} hari</p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm font-bold">Status</p>
                                <span
                                    class="px-3 py-1 rounded bg-yellow-100 text-yellow-800 font-bold text-sm">{{ $submission->status }}</span>
                            </div>
                        </div>

                        @if($submission->reason)
                            <div class="bg-gray-50 p-3 rounded mb-4 border-l-4 border-gray-300">
                                <p class="text-gray-600 text-sm mb-1"><strong>Alasan:</strong></p>
                                <p class="text-gray-800">{{ $submission->reason }}</p>
                            </div>
                        @endif

                        <div class="flex gap-2">
                            <button onclick="approveLeave({{ $submission->id }})"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center justify-center gap-2">
                                <i class="fas fa-check-circle"></i> Setujui
                            </button>
                            <button onclick="openRejectModal({{ $submission->id }})"
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded flex items-center justify-center gap-2">
                                <i class="fas fa-times-circle"></i> Tolak
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow p-8 text-center">
                        <i class="fas fa-check-circle text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">Tidak ada pengajuan cuti yang menunggu</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Approved Submissions -->
        <div id="approved-content" class="tab-content hidden">
            <div class="space-y-4">
                @forelse($approved as $submission)
                    <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-green-400">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-lg font-bold text-gray-800">{{ $submission->user->name }}</p>
                                <p class="text-gray-600">{{ $submission->start_date->format('d-m-Y') }} -
                                    {{ $submission->end_date->format('d-m-Y') }} ({{ $submission->getTotalDays() }} hari)</p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 rounded bg-green-100 text-green-800 font-bold text-sm">Disetujui</span>
                                <p class="text-xs text-gray-500 mt-1">Oleh: {{ $submission->approver->name ?? 'Admin' }}</p>
                                <p class="text-xs text-gray-500">{{ $submission->approved_at->format('d-m-Y H:i') }}</p>
                            </div>
                        </div>
                        @if($submission->reason)
                            <p class="text-gray-700 text-sm">Alasan: {{ $submission->reason }}</p>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400">
                        Belum ada pengajuan cuti yang disetujui
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Rejected Submissions -->
        <div id="rejected-content" class="tab-content hidden">
            <div class="space-y-4">
                @forelse($rejected as $submission)
                    <div class="card-hover bg-white rounded-lg shadow p-6 border-l-4 border-red-400">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-lg font-bold text-gray-800">{{ $submission->user->name }}</p>
                                <p class="text-gray-600">{{ $submission->start_date->format('d-m-Y') }} -
                                    {{ $submission->end_date->format('d-m-Y') }} ({{ $submission->getTotalDays() }} hari)</p>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 rounded bg-red-100 text-red-800 font-bold text-sm">Ditolak</span>
                                <p class="text-xs text-gray-500 mt-1">Oleh: {{ $submission->approver->name ?? 'Admin' }}</p>
                                <p class="text-xs text-gray-500">{{ $submission->approved_at->format('d-m-Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="bg-red-50 p-3 rounded mb-2 border-l-4 border-red-300">
                            <p class="text-red-700 text-sm mb-1"><strong>Alasan Penolakan:</strong></p>
                            <p class="text-red-600">{{ $submission->rejection_reason }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400">
                        Belum ada pengajuan cuti yang ditolak
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const tabName = this.dataset.tab;

                // Hide all content
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                });

                // Show selected content
                document.getElementById(tabName + '-content').classList.remove('hidden');

                // Update active tab styling
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('border-b-2', 'border-blue-600', 'text-blue-600');
                    b.classList.add('text-gray-600', 'hover:text-gray-800');
                });
                this.classList.add('border-b-2', 'border-blue-600', 'text-blue-600');
                this.classList.remove('text-gray-600', 'hover:text-gray-800');
            });
        });

        function approveLeave(submissionId) {
            Swal.fire({
                title: 'Setujui Pengajuan Cuti?',
                text: 'Karyawan akan menerima notifikasi persetujuan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ url("boss/leave-approval") }}/' + submissionId + '/approve', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(r => r.json()).then(data => {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                    }).catch(err => Swal.fire('Error', 'Terjadi kesalahan', 'error'));
                }
            });
        }

        function openRejectModal(submissionId) {
            Swal.fire({
                title: 'Tolak Pengajuan Cuti',
                input: 'textarea',
                inputLabel: 'Alasan Penolakan',
                inputPlaceholder: 'Masukkan alasan penolakan...',
                inputAttributes: {
                    required: true
                },
                showCancelButton: true,
                confirmButtonText: 'Tolak',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    fetch('{{ url("boss/leave-approval") }}/' + submissionId + '/reject', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ rejection_reason: result.value })
                    }).then(r => r.json()).then(data => {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                    }).catch(err => Swal.fire('Error', 'Terjadi kesalahan', 'error'));
                }
            });
        }
    </script>
@endsection