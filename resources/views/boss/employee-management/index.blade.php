@extends('layouts.app')

@section('title', 'Daftar Karyawan')

@section('content')
    <div class="max-w-7xl">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">Daftar Karyawan</h1>
                <p class="text-gray-600">Kelola data semua karyawan pabrik</p>
            </div>
            <a href="{{ route('boss.employees.create') }}"
                class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2">
                <i class="fas fa-user-plus"></i> Tambah Karyawan
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-4 mb-6 flex gap-4">
            <form method="GET" class="flex gap-4 flex-1">
                <select name="status" class="border border-gray-300 rounded px-3 py-2 focus:outline-none">
                    <option value="">-- Semua Status --</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>

                <select name="activity" class="border border-gray-300 rounded px-3 py-2 focus:outline-none">
                    <option value="">-- Semua Aktivitas --</option>
                    <option value="active" {{ request('activity') === 'active' ? 'selected' : '' }}>Aktif (Bulan Ini)</option>
                    <option value="inactive" {{ request('activity') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
            </form>
        </div>

        <!-- Employees Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-700 font-bold">Nama</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-bold">Email</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-bold">Umur</th>
                            <th class="px-4 py-3 text-center text-gray-700 font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                            <tr class="border-b hover:bg-gray-50 text-sm">
                                <td class="px-4 py-4 font-bold text-gray-800">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-black">
                                            {{ substr($employee->name, 0, 1) }}
                                        </div>
                                        {{ $employee->name }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-gray-600 italic">{{ $employee->email }}</td>
                                <td class="px-4 py-4 text-gray-700 font-semibold">{{ $employee->getAge() }} tahun</td>
                                <td class="px-4 py-4 text-center">
                                    <a href="{{ route('boss.employees.show', $employee->id) }}"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-xs font-black uppercase tracking-widest rounded hover:bg-blue-700 transition shadow-sm gap-2">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center text-gray-400">
                                    <i class="fas fa-inbox text-5xl mb-3 block opacity-20"></i>
                                    <p class="italic">Tidak ada data karyawan ditemukan</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($employees->hasPages())
                <div class="px-4 py-4 border-t">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        function toggleStatus(employeeId) {
            Swal.fire({
                title: 'Ubah Status Karyawan?',
                text: 'Status karyawan akan diubah (Aktif/Tidak Aktif)',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ url("boss/employees") }}/' + employeeId + '/toggle-status', {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                        }
                    }).then(r => r.json()).then(data => {
                        Swal.fire('Berhasil!', data.message, 'success').then(() => location.reload());
                    });
                }
            });
        }
    </script>
@endsection