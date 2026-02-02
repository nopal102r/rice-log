@extends('layouts.app')

@section('title', 'Status Pengajuan Cuti')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Status Pengajuan Cuti Saya</h1>
            <p class="text-gray-600">Lihat semua pengajuan cuti Anda dan status persetujuannya</p>
        </div>

        <div class="space-y-4">
            @forelse($submissions as $submission)
                <div
                    class="card-hover bg-white rounded-lg shadow p-6 border-l-4 {{ $submission->status === 'pending' ? 'border-yellow-400' : ($submission->status === 'approved' ? 'border-green-400' : 'border-red-400') }}">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">
                                {{ $submission->start_date->format('d-m-Y') }} s/d {{ $submission->end_date->format('d-m-Y') }}
                            </h3>
                            <p class="text-gray-600">
                                <strong>Total:</strong> {{ $submission->getTotalDays() }} hari
                                @if($submission->reason)
                                    | <strong>Alasan:</strong> {{ $submission->reason }}
                                @endif
                            </p>
                        </div>
                        <span
                            class="px-4 py-2 rounded font-bold text-white {{ $submission->status === 'pending' ? 'bg-yellow-500' : ($submission->status === 'approved' ? 'bg-green-500' : 'bg-red-500') }}">
                            {{ $submission->status === 'pending' ? 'Menunggu' : ($submission->status === 'approved' ? 'Disetujui' : 'Ditolak') }}
                        </span>
                    </div>

                    @if($submission->status === 'approved')
                        <div class="bg-green-50 border border-green-200 rounded p-3 mb-3">
                            <p class="text-green-800 text-sm">
                                <i class="fas fa-check-circle mr-2"></i>
                                <strong>Disetujui oleh:</strong> {{ $submission->approver->name ?? 'Admin' }}
                                pada {{ $submission->approved_at->format('d-m-Y H:i') }}
                            </p>
                        </div>
                    @elseif($submission->status === 'rejected')
                        <div class="bg-red-50 border border-red-200 rounded p-3 mb-3">
                            <p class="text-red-800 text-sm mb-1"><strong>Alasan Penolakan:</strong></p>
                            <p class="text-red-700 text-sm">{{ $submission->rejection_reason }}</p>
                            <p class="text-red-800 text-xs mt-2">Ditolak pada {{ $submission->approved_at->format('d-m-Y H:i') }}
                            </p>
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded p-3 mb-3">
                            <p class="text-yellow-800 text-sm">
                                <i class="fas fa-hourglass-half mr-2"></i>
                                Pengajuan sedang menunggu persetujuan dari atasan Anda.
                            </p>
                        </div>
                    @endif

                    <p class="text-xs text-gray-500 text-right">Dibuat: {{ $submission->created_at->format('d-m-Y H:i') }}</p>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">Anda belum memiliki pengajuan cuti</p>
                    <a href="{{ route('employee.leave.create') }}"
                        class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-plus mr-2"></i> Buat Pengajuan Cuti
                    </a>
                </div>
            @endforelse
        </div>
    </div>
@endsection