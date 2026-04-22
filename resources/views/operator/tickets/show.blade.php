@extends('layouts.app')

@section('title', 'Kelola Aduan')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('operator.tickets.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali ke Antrean
        </a>
        <div class="flex items-center gap-3">
             <span class="text-xs font-bold text-gray-400">STATUS SAAT INI:</span>
             <span class="px-3 py-1 text-xs font-bold rounded-full @if($ticket->status == 'open') bg-blue-100 text-blue-700 @elseif($ticket->status == 'in_progress') bg-yellow-100 text-yellow-700 @elseif($ticket->status == 'resolved') bg-green-100 text-green-700 @else bg-gray-100 text-gray-700 @endif">
                {{ strtoupper($ticket->status) }}
            </span>
        </div>
    </div>

    <div class="grid lg:grid-cols-4 gap-6">
        <!-- Chat Area -->
        <div class="lg:col-span-3 space-y-6">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100 flex flex-col h-[700px]">
                <!-- Header -->
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-start">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">{{ $ticket->subject }}</h1>
                        <p class="text-sm text-gray-500 mt-1">
                            <i class="fas fa-user-circle mr-1"></i> Pelapor: <span class="font-bold">{{ $ticket->user->name }}</span>
                            <span class="mx-2">|</span>
                            <i class="fas fa-clock mr-1"></i> Dibuat: {{ $ticket->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>

                <!-- Messages -->
                <div class="flex-1 p-6 overflow-y-auto space-y-4 bg-[#f0f2f5]" id="chat-container">
                    @foreach($ticket->messages as $msg)
                        <div class="flex {{ $msg->user_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[75%] {{ $msg->user_id == auth()->id() ? 'bg-blue-600 text-white rounded-l-2xl rounded-tr-2xl' : 'bg-white text-gray-800 rounded-r-2xl rounded-tl-2xl shadow-sm' }} p-4">
                                <div class="flex items-center justify-between gap-4 mb-1">
                                    <span class="text-[10px] font-bold opacity-75">{{ $msg->user->role == 'karyawan' ? 'Karyawan' : 'Petugas' }} - {{ $msg->user->name }}</span>
                                    <span class="text-[9px] opacity-75">{{ $msg->created_at->format('H:i') }}</span>
                                </div>
                                <p class="text-sm whitespace-pre-wrap">{{ $msg->message }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Footer / Reply -->
                <div class="p-4 bg-white border-t border-gray-100">
                    <!-- Suggestions Dropdown -->
                    <div id="suggestions-area" class="mb-3 hidden">
                        <p class="text-[10px] font-bold text-gray-400 mb-2 uppercase tracking-widest">Saran Balasan Cepat</p>
                        <div id="suggestions-list" class="flex flex-wrap gap-2">
                            <!-- JS populated -->
                        </div>
                    </div>

                    <form action="{{ route('operator.tickets.reply', $ticket) }}" method="POST">
                        @csrf
                        <div class="flex flex-col gap-3">
                            <textarea name="message" id="reply-message" rows="3" class="w-full rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Tulis balasan atau gunakan saran di atas..." required></textarea>
                            <div class="flex justify-between items-center">
                                <button type="button" onclick="loadSuggestions()" class="text-xs font-bold text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-magic mr-1"></i> Lihat Saran Balasan
                                </button>
                                <button type="submit" class="bg-blue-600 hover:bg-black text-white px-6 py-2 rounded-xl font-bold transition-all flex items-center gap-2">
                                    Kirim Balasan <i class="fas fa-paper-plane text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar controls -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Status Update -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-sync-alt text-yellow-500"></i> Update Status
                </h3>
                <form action="{{ route('operator.tickets.status', $ticket) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-3">
                        @foreach(['open', 'in_progress', 'resolved', 'closed'] as $st)
                            <label class="flex items-center p-3 rounded-xl border {{ $ticket->status == $st ? 'bg-blue-50 border-blue-200 ring-1 ring-blue-200' : 'bg-gray-50 border-gray-100 hover:bg-gray-100 cursor-pointer' }} transition-all">
                                <input type="radio" name="status" value="{{ $st }}" class="hidden" {{ $ticket->status == $st ? 'checked' : '' }} onchange="this.form.submit()">
                                <div class="flex-1">
                                    <p class="text-xs font-bold capitalize {{ $ticket->status == $st ? 'text-blue-700' : 'text-gray-600' }}">{{ $st == 'in_progress' ? 'Dalam Proses' : $st }}</p>
                                </div>
                                @if($ticket->status == $st)
                                    <i class="fas fa-check-circle text-blue-500"></i>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </form>
            </div>

            <!-- SLA Stats -->
            <div class="bg-gray-900 rounded-2xl shadow-xl p-6 text-white">
                <h3 class="font-bold text-sm mb-4 flex items-center gap-2 opacity-80">
                    <i class="fas fa-stopwatch text-blue-400"></i> SLA Tracking
                </h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] uppercase opacity-50 font-bold mb-1">Response Time</p>
                        @if($ticket->first_replied_at)
                            <p class="text-lg font-bold">{{ $ticket->getResponseTimeInMinutes() }} <span class="text-xs font-normal opacity-70">Menit</span></p>
                        @else
                            <p class="text-xs italic opacity-70">Belum ditanggapi</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-[10px] uppercase opacity-50 font-bold mb-1">Resolution Time</p>
                        @if($ticket->resolved_at)
                            <p class="text-lg font-bold">{{ $ticket->getResolutionTimeInMinutes() }} <span class="text-xs font-normal opacity-70">Menit</span></p>
                        @else
                            <p class="text-xs italic opacity-70">Belum selesai</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script>
    const chatContainer = document.getElementById('chat-container');
    chatContainer.scrollTop = chatContainer.scrollHeight;

    function loadSuggestions() {
        const area = document.getElementById('suggestions-area');
        const list = document.getElementById('suggestions-list');
        
        fetch('{{ route("operator.tickets.suggestions", $ticket->category_id) }}')
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    list.innerHTML = '';
                    data.forEach(item => {
                        const btn = document.createElement('button');
                        btn.className = "px-3 py-1 bg-blue-50 text-blue-600 border border-blue-100 rounded-full text-[10px] font-bold hover:bg-blue-600 hover:text-white transition-all";
                        btn.innerText = item.text.substring(0, 30) + '...';
                        btn.onclick = () => {
                            document.getElementById('reply-message').value = item.text;
                        };
                        list.appendChild(btn);
                    });
                } else {
                    list.innerHTML = '<p class="text-[10px] text-gray-400 italic">Tidak ada saran untuk kategori ini. Tambahkan saran di pengaturan jika perlu.</p>';
                }
                area.classList.remove('hidden');
            });
    }
</script>
@endsection
