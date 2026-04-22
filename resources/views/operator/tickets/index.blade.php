@extends('layouts.app')

@section('title', 'Antrean Aduan')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Antrean Aduan</h1>
            <p class="text-gray-500 mt-1">Kelola dan tanggapi masalah karyawan secara real-time.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-2 flex items-center gap-2">
                <span class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></span>
                <span class="text-sm font-bold text-blue-700">{{ $tickets->where('status', 'open')->count() }} Aduan Baru</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tiket</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Pelapor</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Terakhir Update</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900 line-clamp-1">{{ $ticket->subject }}</span>
                                    <span class="text-[10px] text-gray-400 font-medium">{{ $ticket->category->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                        {{ substr($ticket->user->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm text-gray-700">{{ $ticket->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold @if($ticket->status == 'open') bg-blue-100 text-blue-700 @elseif($ticket->status == 'in_progress') bg-yellow-100 text-yellow-700 @elseif($ticket->status == 'resolved') bg-green-100 text-green-700 @else bg-gray-100 text-gray-700 @endif">
                                    {{ strtoupper($ticket->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $ticket->updated_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('operator.tickets.show', $ticket) }}" class="inline-flex items-center gap-2 bg-gray-900 hover:bg-black text-white text-xs font-bold px-4 py-2 rounded-lg transition-all">
                                    Kelola <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-check-circle text-4xl text-gray-200 mb-3"></i>
                                <p>Semua aduan sudah tertangani. Kerja bagus!</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
