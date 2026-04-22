@extends('layouts.app')

@section('title', 'Buat Aduan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('employee.tickets.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2 mb-4">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Aduan
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Buat Aduan Baru</h1>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Form Section -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <form action="{{ route('employee.tickets.store') }}" method="POST" id="ticket-form">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori Masalah</label>
                        <select name="category_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Aduan (Subject)</label>
                        <input type="text" name="subject" id="subject" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: Salah rekap absensi tanggal 20" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi Lengkap</label>
                        <textarea name="description" id="description" rows="6" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Jelaskan detail masalah Anda..." required></textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i> Kirim Aduan
                    </button>
                </form>
            </div>
        </div>

        <!-- Anti-Duplication Section -->
        <div class="lg:col-span-1">
            <div id="duplicate-container" class="bg-blue-50 rounded-xl p-5 border border-blue-100 hidden">
                <div class="flex items-center gap-2 mb-3 text-blue-800">
                    <i class="fas fa-lightbulb text-yellow-500"></i>
                    <h3 class="font-bold text-sm">Aduan Serupa yang Ditemukan</h3>
                </div>
                <div id="duplicate-list" class="space-y-3">
                    <!-- Suggested content here -->
                </div>
                <p class="text-[10px] text-blue-600 mt-4 leading-tight italic">
                    *Jika masalah Anda sudah dilaporkan sebelumnya, mohon tunggu penanganan aduan tersebut untuk menghindari duplikasi.
                </p>
            </div>
            
            <div id="no-duplicate-tip" class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                <p class="text-xs text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Sistem akan otomatis mencari aduan serupa saat Anda mengetik untuk mempercepat penanganan.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script>
    let debounceTimer;
    const subjectInput = document.getElementById('subject');
    const descriptionInput = document.getElementById('description');
    const submitButton = document.querySelector('button[type="submit"]');
    const duplicateContainer = document.getElementById('duplicate-container');
    const noDuplicateTip = document.getElementById('no-duplicate-tip');
    const duplicateList = document.getElementById('duplicate-list');

    function checkDuplicates() {
        const subject = subjectInput.value;
        const description = descriptionInput.value;

        if (subject.length < 3 && description.length < 5) {
            duplicateContainer.classList.add('hidden');
            noDuplicateTip.classList.remove('hidden');
            submitButton.disabled = false;
            submitButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
            submitButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
            return;
        }

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetch('{{ route("employee.tickets.check-duplicate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ subject, description })
            })
            .then(response => response.json())
            .then(data => {
                const headerIcon = duplicateContainer.querySelector('i');
                const headerText = duplicateContainer.querySelector('h3');
                const headerWrapper = headerText.parentElement;

                if (data.length > 0) {
                    duplicateList.innerHTML = '';
                    data.forEach(ticket => {
                        duplicateList.innerHTML += `
                            <div class="bg-white p-3 rounded-lg shadow-sm border-l-4 border-red-500">
                                <h4 class="text-xs font-bold text-gray-800 mb-1">${ticket.subject}</h4>
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] uppercase font-bold text-red-500">${ticket.status}</span>
                                    <a href="/employee/tickets/${ticket.id}" target="_blank" class="text-[10px] text-blue-600 underline">Lihat Detail</a>
                                </div>
                            </div>
                        `;
                    });
                    
                    // Style as Warning
                    duplicateContainer.classList.remove('hidden', 'bg-blue-50', 'border-blue-100');
                    duplicateContainer.classList.add('bg-red-50', 'border-red-200');
                    headerWrapper.classList.remove('text-blue-800');
                    headerWrapper.classList.add('text-red-800');
                    headerText.innerText = "DUPLIKASI TERDETEKSI!";
                    headerIcon.classList.replace('fa-lightbulb', 'fa-exclamation-triangle');
                    headerIcon.classList.replace('text-yellow-500', 'text-red-500');
                    
                    noDuplicateTip.classList.add('hidden');
                    
                    // Disable submit button
                    submitButton.disabled = true;
                    submitButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    submitButton.classList.add('bg-gray-400', 'cursor-not-allowed');
                    
                } else {
                    duplicateContainer.classList.add('hidden');
                    noDuplicateTip.classList.remove('hidden');
                    
                    // Reset to normal style
                    duplicateContainer.classList.remove('bg-red-50', 'border-red-200');
                    duplicateContainer.classList.add('bg-blue-50', 'border-blue-100');
                    headerWrapper.classList.remove('text-red-800');
                    headerWrapper.classList.add('text-blue-800');
                    headerText.innerText = "Aduan Serupa yang Ditemukan";
                    headerIcon.classList.replace('fa-exclamation-triangle', 'fa-lightbulb');
                    headerIcon.classList.replace('text-red-500', 'text-yellow-500');

                    // Enable submit button
                    submitButton.disabled = false;
                    submitButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    submitButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }
            });
        }, 800);
    }

    [subjectInput, descriptionInput].forEach(input => {
        input.addEventListener('input', checkDuplicates);
    });
</script>
@endsection
