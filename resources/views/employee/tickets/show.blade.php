@extends('layouts.app')

@section('title', 'Detail Aduan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 text-sm">
        <a href="{{ route('employee.tickets.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
        <div class="flex items-center gap-4">
            <span class="text-gray-500">ID Tiket: #{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</span>
            <span class="px-3 py-1 text-xs font-bold rounded-full @if($ticket->status == 'open') bg-blue-100 text-blue-700 @elseif($ticket->status == 'in_progress') bg-yellow-100 text-yellow-700 @elseif($ticket->status == 'resolved') bg-green-100 text-green-700 @else bg-gray-100 text-gray-700 @endif">
                {{ strtoupper($ticket->status) }}
            </span>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Chat History -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
                <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                    <h1 class="text-xl font-bold text-gray-800">{{ $ticket->subject }}</h1>
                    <div class="flex items-center gap-2 text-xs text-gray-500 mt-2">
                        <i class="fas fa-folder-open"></i> {{ $ticket->category->name }}
                        <span>•</span>
                        <i class="fas fa-clock"></i> {{ $ticket->created_at->format('d M Y, H:i') }}
                    </div>
                </div>

                <div class="p-6 max-h-[500px] overflow-y-auto space-y-4" id="chat-container">
                    @foreach($ticket->messages as $msg)
                        <div class="flex {{ $msg->user_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[85%] {{ $msg->user_id == auth()->id() ? 'bg-blue-600 text-white rounded-l-2xl rounded-tr-2xl' : 'bg-gray-100 text-gray-800 rounded-r-2xl rounded-tl-2xl' }} p-4 shadow-sm">
                                <div class="flex items-center justify-between gap-4 mb-1">
                                    <span class="text-[10px] font-bold opacity-75">{{ $msg->user->name }}</span>
                                    <span class="text-[9px] opacity-75">{{ $msg->created_at->format('H:i') }}</span>
                                </div>
                                <p class="text-sm whitespace-pre-wrap">{{ $msg->message }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($ticket->user_id == auth()->id())
                    @if($ticket->status != 'closed')
                        <div class="p-4 bg-white border-t border-gray-100">
                            <form action="{{ route('employee.tickets.message', $ticket) }}" method="POST" class="flex gap-2">
                                @csrf
                                <input type="text" name="message" class="flex-1 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Tulis balasan..." required autocomplete="off">
                                <button type="submit" class="bg-blue-600 text-white p-3 rounded-xl hover:bg-blue-700 transition-all">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="p-4 bg-gray-50 text-center text-sm text-gray-500 italic">
                            <i class="fas fa-lock mr-1"></i> Percakapan ini telah ditutup.
                        </div>
                    @endif
                @else
                    <div class="p-4 bg-blue-50 text-center text-xs text-blue-700 font-medium">
                        <i class="fas fa-info-circle mr-1"></i> Anda sedang melihat aduan serupa yang dilaporkan oleh rekan lain.
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Side: Info & Rating -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-500"></i> Informasi Petugas
                </h3>
                @if($ticket->operator)
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                            {{ substr($ticket->operator->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">{{ $ticket->operator->name }}</p>
                            <p class="text-[10px] text-gray-500 uppercase tracking-wider">Operator Penanggung Jawab</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4 text-sm text-gray-400 italic">
                        Menunggu petugas...
                    </div>
                @endif
            </div>

            @if(in_array($ticket->status, ['resolved', 'closed']) && $ticket->user_id == auth()->id())
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100 text-center">
                    <h3 class="font-bold text-gray-800 mb-2">Penilaian Anda</h3>
                    <p class="text-xs text-gray-500 mb-4">Bagaimana tingkat kepuasan Anda terhadap penanganan aduan ini?</p>
                    
                    @if(!$ticket->rating)
                        <form action="{{ route('employee.tickets.rate', $ticket) }}" method="POST">
                            @csrf
                            <div class="flex items-center justify-center gap-2 mb-6 text-2xl text-gray-300 transition-colors">
                                @for($i=1; $i<=5; $i++)
                                    <input type="radio" name="rating" id="star-{{ $i }}" value="{{ $i }}" class="hidden peer" required>
                                    <label for="star-{{ $i }}" class="cursor-pointer hover:text-yellow-400 peer-checked:text-yellow-400">
                                        <i class="fas fa-star" id="star-icon-{{ $i }}"></i>
                                    </label>
                                @endfor
                            </div>
                            <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-yellow-900 font-bold py-2 rounded-xl text-sm transition-all">
                                Kirim Penilaian
                            </button>
                        </form>
                    @else
                        <div class="flex items-center justify-center gap-2 text-2xl text-yellow-400 mb-2">
                            @for($i=1; $i<=5; $i++)
                                <i class="fa{{ $i <= $ticket->rating ? 's' : 'r' }} fa-star"></i>
                            @endfor
                        </div>
                        <p class="text-sm font-bold text-yellow-600">Terima Kasih!</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script>
    // Star rating hover effect
    const stars = document.querySelectorAll('input[name="rating"]');
    stars.forEach(star => {
        star.addEventListener('change', function() {
            const val = this.value;
            for(let i=1; i<=5; i++) {
                const icon = document.getElementById('star-icon-' + i);
                if (i <= val) {
                    icon.parentElement.style.color = '#fbbf24'; // yellow-400
                } else {
                    icon.parentElement.style.color = '#d1d5db'; // gray-300
                }
            }
        });
    });

    // Auto-scroll chat to bottom
    const container = document.getElementById('chat-container');
    container.scrollTop = container.scrollHeight;
</script>
@endsection
