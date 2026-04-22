@extends('layouts.app')

@section('title', 'Aduan Saya')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Aduan Saya</h1>
        <a href="{{ route('employee.tickets.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-all">
            <i class="fas fa-plus"></i> Buat Aduan Baru
        </a>
    </div>

    @if($tickets->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-ticket-alt text-3xl text-gray-400"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-600">Belum ada aduan</h2>
            <p class="text-gray-500 mt-2">Semua aduan Anda akan muncul di sini.</p>
        </div>
    @else
        <div class="grid gap-4">
            @foreach($tickets as $ticket)
                <a href="{{ route('employee.tickets.show', $ticket) }}" class="block bg-white rounded-xl shadow-sm hover:shadow-md transition-all p-5 border-l-4 @if($ticket->status == 'open') border-blue-500 @elseif($ticket->status == 'in_progress') border-yellow-500 @elseif($ticket->status == 'resolved') border-green-500 @else border-gray-400 @endif">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full @if($ticket->status == 'open') bg-blue-100 text-blue-700 @elseif($ticket->status == 'in_progress') bg-yellow-100 text-yellow-700 @elseif($ticket->status == 'resolved') bg-green-100 text-green-700 @else bg-gray-100 text-gray-700 @endif">
                                    {{ strtoupper($ticket->status) }}
                                </span>
                                <span class="text-xs text-gray-500">{{ $ticket->category->name }}</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800">{{ $ticket->subject }}</h3>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-1">{{ Str::limit($ticket->description, 100) }}</p>
                        </div>
                        <div class="text-right whitespace-nowrap">
                            <span class="text-xs text-gray-400 block">{{ $ticket->created_at->diffForHumans() }}</span>
                            @if($ticket->rating)
                                <div class="flex items-center justify-end gap-1 mt-1 text-yellow-400">
                                    @for($i=1; $i<=5; $i++)
                                        <i class="fa{{ $i <= $ticket->rating ? 's' : 'r' }} fa-star text-xs"></i>
                                    @endfor
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
