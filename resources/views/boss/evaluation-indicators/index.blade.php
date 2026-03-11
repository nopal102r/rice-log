@extends('layouts.app')

@section('title', 'Manajemen Indikator Penilaian')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Indikator Penilaian</h1>
            <p class="text-gray-600">Atur kriteria penilaian kinerja karyawan secara dinamis.</p>
        </div>
        <button onclick="openAddIndicatorModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all active:scale-95 flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Indikator
        </button>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @forelse($indicators as $indicator)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden group hover:shadow-md transition-all">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-xl font-black text-gray-800 uppercase tracking-tight">{{ $indicator->name }}</h2>
                    <div class="flex gap-2">
                        <button onclick="openEditIndicatorModal({{ $indicator->id }}, '{{ $indicator->name }}')" class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-colors">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('boss.evaluation-indicators.destroy', $indicator->id) }}" method="POST" onsubmit="return confirm('Hapus indikator ini dan semua deskripsinya?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="p-6">
                    <div class="mb-4 flex justify-between items-center">
                        <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Deskripsi Penilaian</span>
                        <button onclick="openAddDescriptionModal({{ $indicator->id }})" class="text-xs bg-gray-100 hover:bg-blue-600 hover:text-white px-3 py-1.5 rounded-lg font-bold transition-all">
                            <i class="fas fa-plus mr-1"></i> Tambah Item
                        </button>
                    </div>
                    <ul class="space-y-3">
                        @forelse($indicator->descriptions as $desc)
                            <li class="flex justify-between items-center bg-gray-50 p-3 rounded-xl border border-transparent hover:border-gray-200 transition-all group/item">
                                <span class="text-gray-700 font-medium">{{ $desc->name }}</span>
                                <div class="flex gap-1 opacity-0 group-hover/item:opacity-100 transition-opacity">
                                    <button onclick="openEditDescriptionModal({{ $desc->id }}, '{{ $desc->name }}')" class="text-blue-500 hover:text-blue-700 p-1">
                                        <i class="fas fa-pencil-alt text-xs"></i>
                                    </button>
                                    <form action="{{ route('boss.evaluation-descriptions.destroy', $desc->id) }}" method="POST" onsubmit="return confirm('Hapus item penilaian ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-1">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @empty
                            <li class="text-center py-4 text-gray-400 text-sm italic border-2 border-dashed border-gray-100 rounded-xl">
                                Belum ada deskripsi. Klik "Tambah Item" untuk memulai.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @empty
            <div class="lg:col-span-2 bg-white rounded-3xl p-16 text-center border-2 border-dashed border-gray-200">
                <div class="w-24 h-24 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Belum ada Indikator Penilaian</h3>
                <p class="text-gray-500 mb-8 max-w-sm mx-auto">Buat indikator seperti "Disiplin", "Teamwork", atau "Kinerja" untuk mulai menilai karyawan Anda.</p>
                <button onclick="openAddIndicatorModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-2xl font-black shadow-xl shadow-blue-100 transition-all active:scale-95">
                    Buat Indikator Pertama
                </button>
            </div>
        @endforelse
    </div>
</div>

<!-- Modals -->

<!-- Add/Edit Indicator Modal -->
<div id="indicatorModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden scale-95 transition-transform duration-300 transform" id="indicatorModalContent">
        <div class="bg-blue-600 px-8 py-6 text-white flex justify-between items-center">
            <h3 id="indicatorModalTitle" class="text-xl font-bold">Tambah Indikator</h3>
            <button onclick="closeIndicatorModal()" class="text-white/70 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form id="indicatorForm" method="POST" class="p-8">
            @csrf
            <input type="hidden" name="_method" id="indicatorMethod" value="POST">
            <div class="mb-6">
                <label class="block text-gray-500 text-[10px] font-black uppercase tracking-widest mb-2">Nama Indikator</label>
                <input type="text" name="name" id="indicatorName" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-6 py-4 outline-none focus:border-blue-500 transition-all font-bold text-gray-800" placeholder="Contoh: Disiplin, Kerjasama, dll.">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-black shadow-lg shadow-blue-100 transition-all active:scale-95">
                Simpan Indikator
            </button>
        </form>
    </div>
</div>

<!-- Add/Edit Description Modal -->
<div id="descriptionModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden scale-95 transition-transform duration-300 transform" id="descriptionModalContent">
        <div class="bg-indigo-600 px-8 py-6 text-white flex justify-between items-center">
            <h3 id="descriptionModalTitle" class="text-xl font-bold">Tambah Deskripsi</h3>
            <button onclick="closeDescriptionModal()" class="text-white/70 hover:text-white"><i class="fas fa-times"></i></button>
        </div>
        <form id="descriptionForm" method="POST" class="p-8">
            @csrf
            <input type="hidden" name="_method" id="descriptionMethod" value="POST">
            <div class="mb-6">
                <label class="block text-gray-500 text-[10px] font-black uppercase tracking-widest mb-2">Nama Deskripsi / Item Penilaian</label>
                <input type="text" name="name" id="descriptionName" required class="w-full bg-gray-50 border-2 border-gray-100 rounded-2xl px-6 py-4 outline-none focus:border-indigo-500 transition-all font-bold text-gray-800" placeholder="Contoh: Ketepatan Waktu, Komunikasi, dll.">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-black shadow-lg shadow-indigo-100 transition-all active:scale-95">
                Simpan Deskripsi
            </button>
        </form>
    </div>
</div>

@endsection

@section('extra-js')
<script>
    function openAddIndicatorModal() {
        const modal = document.getElementById('indicatorModal');
        const form = document.getElementById('indicatorForm');
        const method = document.getElementById('indicatorMethod');
        const title = document.getElementById('indicatorModalTitle');
        const name = document.getElementById('indicatorName');

        form.action = "{{ route('boss.evaluation-indicators.store') }}";
        method.value = "POST";
        title.innerText = "Tambah Indikator";
        name.value = "";

        modal.classList.remove('hidden');
        setTimeout(() => document.getElementById('indicatorModalContent').classList.remove('scale-95'), 10);
    }

    function openEditIndicatorModal(id, currentName) {
        const modal = document.getElementById('indicatorModal');
        const form = document.getElementById('indicatorForm');
        const method = document.getElementById('indicatorMethod');
        const title = document.getElementById('indicatorModalTitle');
        const name = document.getElementById('indicatorName');

        form.action = "{{ url('boss/evaluation-indicators') }}/" + id;
        method.value = "PUT";
        title.innerText = "Edit Indikator";
        name.value = currentName;

        modal.classList.remove('hidden');
        setTimeout(() => document.getElementById('indicatorModalContent').classList.remove('scale-95'), 10);
    }

    function closeIndicatorModal() {
        document.getElementById('indicatorModalContent').classList.add('scale-95');
        setTimeout(() => document.getElementById('indicatorModal').classList.add('hidden'), 300);
    }

    function openAddDescriptionModal(indicatorId) {
        const modal = document.getElementById('descriptionModal');
        const form = document.getElementById('descriptionForm');
        const method = document.getElementById('descriptionMethod');
        const title = document.getElementById('descriptionModalTitle');
        const name = document.getElementById('descriptionName');

        form.action = "{{ url('boss/evaluation-indicators') }}/" + indicatorId + "/descriptions";
        method.value = "POST";
        title.innerText = "Tambah Deskripsi";
        name.value = "";

        modal.classList.remove('hidden');
        setTimeout(() => document.getElementById('descriptionModalContent').classList.remove('scale-95'), 10);
    }

    function openEditDescriptionModal(id, currentName) {
        const modal = document.getElementById('descriptionModal');
        const form = document.getElementById('descriptionForm');
        const method = document.getElementById('descriptionMethod');
        const title = document.getElementById('descriptionModalTitle');
        const name = document.getElementById('descriptionName');

        form.action = "{{ url('boss/evaluation-descriptions') }}/" + id;
        method.value = "PUT";
        title.innerText = "Edit Deskripsi";
        name.value = currentName;

        modal.classList.remove('hidden');
        setTimeout(() => document.getElementById('descriptionModalContent').classList.remove('scale-95'), 10);
    }

    function closeDescriptionModal() {
        document.getElementById('descriptionModalContent').classList.add('scale-95');
        setTimeout(() => document.getElementById('descriptionModal').classList.add('hidden'), 300);
    }

    // Close modals on backdrop click
    window.onclick = function(event) {
        if (event.target == document.getElementById('indicatorModal')) closeIndicatorModal();
        if (event.target == document.getElementById('descriptionModal')) closeDescriptionModal();
    }
</script>
@endsection
