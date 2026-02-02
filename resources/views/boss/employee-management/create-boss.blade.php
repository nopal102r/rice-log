@extends('layouts.app')

@section('title', 'Tambah Bos/Manajer Baru')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Tambah Bos/Manajer Baru</h1>
            <p class="text-gray-600">Daftarkan bos/manajer baru ke sistem</p>
        </div>

        <div class="bg-white rounded-lg shadow p-8">
            <form id="addBossForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-2">
                            <i class="fas fa-user text-purple-600 mr-2"></i> Nama Lengkap
                        </label>
                        <input type="text" name="name" id="name" required
                            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-purple-500"
                            placeholder="Nama bos/manajer">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-2">
                            <i class="fas fa-envelope text-purple-600 mr-2"></i> Email
                        </label>
                        <input type="email" name="email" id="email" required
                            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-purple-500"
                            placeholder="example@mail.com">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-2">
                            <i class="fas fa-phone text-purple-600 mr-2"></i> Nomor Telepon
                        </label>
                        <input type="tel" name="phone" id="phone" required
                            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-purple-500"
                            placeholder="081234567890">
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-2">
                            <i class="fas fa-birthday-cake text-purple-600 mr-2"></i> Tanggal Lahir
                        </label>
                        <input type="date" name="date_of_birth" id="date_of_birth" required
                            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-purple-500">
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-2">
                            <i class="fas fa-lock text-purple-600 mr-2"></i> Password
                        </label>
                        <input type="password" name="password" id="password" required minlength="8"
                            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-purple-500"
                            placeholder="Minimal 8 karakter">
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label class="block text-gray-800 font-bold mb-2">
                            <i class="fas fa-lock text-purple-600 mr-2"></i> Konfirmasi Password
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            minlength="8"
                            class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-purple-500"
                            placeholder="Konfirmasi password">
                    </div>
                </div>

                <!-- Address -->
                <div class="mt-6">
                    <label class="block text-gray-800 font-bold mb-2">
                        <i class="fas fa-home text-purple-600 mr-2"></i> Alamat (Opsional)
                    </label>
                    <textarea name="address" id="address" rows="3"
                        class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:border-purple-500"
                        placeholder="Alamat rumah bos/manajer"></textarea>
                </div>

                <div class="bg-purple-50 border border-purple-200 rounded p-4 mt-6 mb-6">
                    <p class="text-purple-800 text-sm">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Catatan:</strong> Bos/Manajer akan memiliki akses penuh untuk mengelola karyawan,
                        persetujuan cuti, dan verifikasi setor.
                    </p>
                </div>

                <div class="flex gap-4">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-bold py-3 rounded-lg flex items-center justify-center gap-2">
                        <i class="fas fa-plus"></i> Tambah Bos/Manajer
                    </button>
                    <a href="{{ route('boss.employees.index') }}"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 rounded-lg flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        document.getElementById('addBossForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            const dateOfBirth = document.getElementById('date_of_birth').value;
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            const address = document.getElementById('address').value;

            if (password !== passwordConfirmation) {
                Swal.fire('Error', 'Password tidak cocok!', 'error');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Tambahkan ' + name + ' sebagai bos/manajer baru?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tambah!',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    try {
                        const response = await fetch('{{ route("boss.boss-management.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            },
                            body: JSON.stringify({
                                name: name,
                                email: email,
                                phone: phone,
                                date_of_birth: dateOfBirth,
                                password: password,
                                password_confirmation: passwordConfirmation,
                                address: address
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            Swal.fire('Berhasil!', data.message, 'success').then(() => {
                                window.location.href = '{{ route("boss.employees.index") }}';
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'Terjadi kesalahan: ' + error.message, 'error');
                    }
                }
            });
        });
    </script>
@endsection