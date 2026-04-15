<?php

use App\Models\User;
use function Pest\Laravel\{actingAs, get};

test('halaman dashboard evaluasi bisa diakses oleh boss', function () {
    // 1. Siapkan data: Buat user dengan role boss ('bos')
    $boss = User::factory()->create(['role' => 'bos']);

    // 2. Jalankan aksi: Masuk sebagai boss dan buka halaman evaluasi
    $response = actingAs($boss)->get('/boss/evaluations');

    // 3. Pastikan hasilnya sesuai (Status 200 OK)
    $response->assertStatus(200);
});

test('karyawan tidak boleh akses halaman evaluasi boss', function () {
    // 1. Siapkan data: Buat user dengan role karyawan
    $employee = User::factory()->create(['role' => 'karyawan']);

    // 2. Jalankan aksi: Mencoba buka halaman boss
    $response = actingAs($employee)->get('/boss/evaluations');

    // 3. Pastikan hasilnya ditolak (Status 302/Redirect atau 403)
    // Biasanya diredirect ke dashboard employee
    $response->assertStatus(302);
});
