<?php

namespace Database\Seeders;

use App\Models\EvaluationIndicator;
use Illuminate\Database\Seeder;

class EvaluationIndicatorSeeder extends Seeder
{
    public function run(): void
    {
        // Data dummy: Daftar kategori (Indikator) dan butir pertanyaannya (Deskripsi)
        $data = [
            'Disiplin' => [
                'Ketepatan Waktu Datang',
                'Kepatuhan terhadap Aturan Kerja',
                'Konsistensi Kehadiran',
                'Kerapihan & Kebersihan Area Kerja',
                'Penyelesaian Tugas Tepat Waktu',
            ],
            // ... (Kategori lainnya)
        ];

        // Looping untuk memasukkan data ke database
        foreach ($data as $indicatorName => $descriptions) {
            // Simpan nama kategori ke tabel indicators
            $indicator = EvaluationIndicator::create(['name' => $indicatorName]);

            // Looping butir pertanyaan untuk kategori tersebut
            foreach ($descriptions as $descName) {
                // Simpan butir pertanyaan yang terhubung ke indikator di atas
                $indicator->descriptions()->create([
                    'name' => $descName
                ]);
            }
        }
    }
}