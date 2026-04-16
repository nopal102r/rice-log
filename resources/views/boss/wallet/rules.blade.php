@extends('layouts.app')

@section('title', 'Aturan Dompet Integritas')

@section('content')
<div class="max-w-7xl mx-auto pb-12">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Aturan Dompet Integritas</h1>
        <p class="text-gray-600">Sistem otomatis point reward dan penalty berdasarkan absensi karyawan.</p>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-xl mb-8 shadow-sm font-bold">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Buat Baru -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-200">
            <h2 class="text-xl font-black text-gray-800 mb-6 border-b pb-4">📝 Buat Aturan Baru</h2>
            <form action="{{ route('boss.wallet.storeRule') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Aturan</label>
                    <input type="text" name="rule_name" required class="w-full border-gray-300 bg-gray-50 rounded-xl focus:ring-blue-500 focus:border-blue-500" placeholder="Cth: Datang Pagi">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Target Role</label>
                    <select name="target_role" class="w-full border-gray-300 bg-gray-50 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua (Global)</option>
                        <option value="karyawan">Hanya Karyawan</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Kondisi</label>
                    <select name="condition_operator" class="w-full border-gray-300 bg-gray-50 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                        <option value="<">Kurang dari Jam (<)</option>
                        <option value=">">Lebih dari Jam (>)</option>
                        <option value="BETWEEN">Diantara Jam (Start,End)</option>
                        <option value="STATUS_EQUALS">Berdasarkan Status (Hadir/Sakit/Izin/Alpa)</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nilai Kondisi (Jam)</label>
                    <input type="text" name="condition_value" required class="w-full border-gray-300 bg-gray-50 rounded-xl focus:ring-blue-500 focus:border-blue-500" placeholder="Cth: 08:00 atau 06:30,07:00">
                    <p class="text-xs text-gray-400 mt-1 font-semibold">Gunakan format HH:MM, atau ketik 'alpa' jika mode status.</p>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Poin Modifier (+ / -)</label>
                    <input type="number" name="point_modifier" required class="w-full border-gray-300 bg-gray-50 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-3" placeholder="Contoh: 5 (untuk plus) atau -3 (minus)">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white font-black uppercase tracking-wider py-3 rounded-xl hover:bg-blue-700 transition shadow-md shadow-blue-200">
                    Simpan Aturan
                </button>
            </form>
        </div>

        <!-- Tabel Daftar Aturan -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/80 border-b border-gray-200 uppercase text-xs tracking-widest text-gray-500">
                            <tr>
                                <th class="px-6 py-4 font-bold">Aturan</th>
                                <th class="px-6 py-4 font-bold">Target</th>
                                <th class="px-6 py-4 font-bold">Syarat (Waktu)</th>
                                <th class="px-6 py-4 text-center font-bold">Poin</th>
                                <th class="px-6 py-4 text-center font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($rules as $rule)
                            <tr class="hover:bg-blue-50/50 transition">
                                <td class="px-6 py-4 font-bold text-gray-800">{{ $rule->rule_name }}</td>
                                <td class="px-6 py-4 text-gray-500 text-xs font-bold">{{ $rule->target_role ? strtoupper($rule->target_role) : 'GLOBAL' }}</td>
                                <td class="px-6 py-4 text-gray-700">
                                    <span class="bg-white border border-gray-200 shadow-sm px-2 py-1 rounded-lg text-xs font-mono font-bold">{{ $rule->condition_operator }} {{ $rule->condition_value }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($rule->point_modifier > 0)
                                        <span class="text-green-700 font-extrabold bg-green-100 border border-green-200 px-3 py-1 rounded-xl">+{{ $rule->point_modifier }}</span>
                                    @else
                                        <span class="text-red-700 font-extrabold bg-red-100 border border-red-200 px-3 py-1 rounded-xl">{{ $rule->point_modifier }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-4">
                                        <button type="button" 
                                            onclick="openEditModal({{ $rule->id }}, '{{ addslashes($rule->rule_name) }}', '{{ $rule->target_role }}', '{{ $rule->condition_operator }}', '{{ addslashes($rule->condition_value) }}', {{ $rule->point_modifier }})" 
                                            class="text-blue-500 hover:text-blue-700 bg-blue-50 px-3 py-2 rounded-lg transition" title="Edit Aturan">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('boss.wallet.destroyRule', $rule->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus aturan ini secara permanen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-500 hover:text-red-700 bg-red-50 px-3 py-2 rounded-lg transition" title="Hapus Aturan">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 font-bold bg-gray-50/50">
                                    Belum ada aturan poin yang dibuat.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Aturan -->
<div id="editModal" class="hidden fixed inset-0 z-[60] bg-gray-900/40 backdrop-blur-sm items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="editModalContent">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="text-xl font-bold text-gray-800">Edit Aturan Poin</h3>
            <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 bg-white shadow-sm w-8 h-8 rounded-full flex items-center justify-center">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editForm" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Aturan</label>
                <input type="text" name="rule_name" id="edit_rule_name" required class="w-full border-gray-300 bg-gray-50 rounded-xl focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Target Role</label>
                <select name="target_role" id="edit_target_role" class="w-full border-gray-300 bg-gray-50 rounded-xl focus:ring-blue-500">
                    <option value="">Semua (Global)</option>
                    <option value="karyawan">Hanya Karyawan</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Kondisi</label>
                <select name="condition_operator" id="edit_condition_operator" class="w-full border-gray-300 bg-gray-50 rounded-xl focus:ring-blue-500">
                    <option value="<">Kurang dari Jam (<)</option>
                    <option value=">">Lebih dari Jam (>)</option>
                    <option value="BETWEEN">Diantara Jam (Start,End)</option>
                    <option value="STATUS_EQUALS">Berdasarkan Status (Hadir/Sakit/Izin/Alpa)</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-700 mb-1">Nilai Kondisi (Jam)</label>
                <input type="text" name="condition_value" id="edit_condition_value" required class="w-full border-gray-300 bg-gray-50 rounded-xl focus:ring-blue-500">
            </div>
            <div class="mb-8">
                <label class="block text-sm font-bold text-gray-700 mb-1">Poin Modifier</label>
                <input type="number" name="point_modifier" id="edit_point_modifier" required class="w-full border-gray-300 bg-gray-50 rounded-xl focus:ring-blue-500">
            </div>
            <div class="flex gap-4">
                <button type="button" onclick="closeEditModal()" class="w-1/3 bg-white text-gray-700 font-bold py-3 rounded-xl border border-gray-300 hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" class="w-2/3 bg-blue-600 text-white font-bold py-3 rounded-xl shadow-md shadow-blue-200 hover:bg-blue-700 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('extra-js')
<script>
    function openEditModal(id, name, target, op, val, point) {
        document.getElementById('edit_rule_name').value = name;
        document.getElementById('edit_target_role').value = target || '';
        document.getElementById('edit_condition_operator').value = op;
        document.getElementById('edit_condition_value').value = val;
        document.getElementById('edit_point_modifier').value = point;
        
        let path = "{{ url('boss/wallet/rules') }}/" + id;
        document.getElementById('editForm').action = path;
        
        const modal = document.getElementById('editModal');
        const content = document.getElementById('editModalContent');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Setup animation
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeEditModal() {
        const modal = document.getElementById('editModal');
        const content = document.getElementById('editModalContent');
        
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
</script>
@endsection
