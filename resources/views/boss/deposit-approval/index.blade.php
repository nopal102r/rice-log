@extends('layouts.app')

@section('title', 'Verifikasi Setor Beras')

@section('content')
    <div class="max-w-6xl">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Verifikasi Setor Beras</h1>
            <p class="text-gray-600">Verifikasi setor beras dari karyawan</p>
        </div>

        @if($deposits->count() > 0)
            <div class="space-y-4 mb-8">
                @foreach($deposits as $deposit)
                    <div class="card-hover bg-white rounded-lg shadow overflow-hidden border-l-4 border-orange-400">
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                                <div>
                                    <p class="text-gray-600 text-sm font-bold">Karyawan</p>
                                    <p class="text-lg font-bold text-gray-800">{{ $deposit->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $deposit->user->email }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm font-bold">Tanggal Setor</p>
                                    <p class="text-lg font-bold text-gray-800">{{ $deposit->created_at->format('d-m-Y H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm font-bold">Detail Pekerjaan</p>
                                    @if($deposit->user->isSales())
                                        <p class="text-lg font-bold text-gray-800">{{ $deposit->weight }} kg</p>
                                        <p class="text-xs text-gray-500">Estimasi: {{ $deposit->weight / 10 }} - {{ $deposit->weight / 25 }} karung</p>
                                    @elseif($deposit->type === 'land_management')
                                        <p class="text-lg font-bold text-gray-800">{{ $deposit->box_count }} Kotak</p>
                                        <p class="text-xs text-gray-500">Urus Lahan</p>
                                    @else
                                        <p class="text-2xl font-bold text-orange-600">{{ $deposit->weight }} kg</p>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm font-bold">Total Nilai Barang</p>
                                    @php
                                        $displayTotal = $deposit->total_price;
                                        if (!$displayTotal || $displayTotal == 0) {
                                            if ($deposit->user && $deposit->user->isSales()) {
                                                $displayTotal = $deposit->money_amount;
                                            } else {
                                                $displayTotal = ($deposit->weight ?? 0) * ($deposit->price_per_kg ?? 12000);
                                            }
                                        }
                                    @endphp
                                    <p class="text-xl font-bold text-gray-800">Rp
                                        {{ number_format($displayTotal, 0, ',', '.') }}</p>
                                    @if($deposit->user && $deposit->user->isSales())
                                        <p class="text-xs text-blue-500 font-bold">Uang Setoran Sales</p>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm font-bold">Estimasi Upah</p>
                                    <p class="text-2xl font-bold text-green-600">Rp
                                        {{ number_format($deposit->wage_amount, 0, ',', '.') }}</p>
                                    <p class="text-xs text-gray-400">Gaji yang akan diterima</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm font-bold">Status</p>
                                    <span
                                        class="px-3 py-1 rounded bg-yellow-100 text-yellow-800 font-bold text-sm">{{ $deposit->status }}</span>
                                </div>
                            </div>

                            @if($deposit->photo)
                                <div class="mb-4">
                                    <p class="text-gray-600 text-sm font-bold mb-2">Foto Beras:</p>
                                    <img src="{{ asset('storage/' . $deposit->photo) }}" alt="Foto Beras"
                                        class="max-w-xs rounded border border-gray-200">
                                </div>
                            @endif

                            @if($deposit->notes)
                                <div class="bg-gray-50 p-3 rounded mb-4 border-l-4 border-gray-300">
                                    <p class="text-gray-600 text-sm mb-1"><strong>Catatan:</strong></p>
                                    <p class="text-gray-800">{{ $deposit->notes }}</p>
                                </div>
                            @endif

                            <div class="flex gap-2">
                                <button onclick="verifyDeposit({{ $deposit->id }})"
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center justify-center gap-2">
                                    <i class="fas fa-check-circle"></i> Verifikasi
                                </button>
                                <button onclick="openRejectModal({{ $deposit->id }})"
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded flex items-center justify-center gap-2">
                                    <i class="fas fa-times-circle"></i> Tolak
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($deposits->hasPages())
                <div class="bg-white rounded-lg shadow p-4">
                    {{ $deposits->links() }}
                </div>
            @endif
        @else
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <i class="fas fa-check-circle text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">Tidak ada setor beras yang menunggu verifikasi</p>
                <p class="text-gray-400 text-sm mt-2">Semua setor beras sudah diverifikasi atau ditolak</p>
            </div>
        @endif
    </div>
@endsection

@section('extra-js')
    <script>
        function verifyDeposit(depositId) {
            Swal.fire({
                title: 'Verifikasi Setor Beras?',
                text: 'Setor ini akan diverifikasi dan karyawan akan menerima notifikasi.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Verifikasi!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ url("boss/deposit-approval") }}/' + depositId + '/verify', {
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

        function openRejectModal(depositId) {
            Swal.fire({
                title: 'Tolak Setor Beras',
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
                    fetch('{{ url("boss/deposit-approval") }}/' + depositId + '/reject', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ reason: result.value })
                    }).then(r => r.json()).then(data => {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                    }).catch(err => Swal.fire('Error', 'Terjadi kesalahan', 'error'));
                }
            });
        }
    </script>
@endsection