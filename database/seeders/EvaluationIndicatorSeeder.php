<?php

namespace Database\Seeders;

use App\Models\EvaluationIndicator;
use Illuminate\Database\Seeder;

class EvaluationIndicatorSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Disiplin' => [
                'Ketepatan Waktu Datang',
                'Kepatuhan terhadap Aturan Kerja',
                'Konsistensi Kehadiran',
                'Kerapihan & Kebersihan Area Kerja',
                'Penyelesaian Tugas Tepat Waktu',
            ],
            'Teamwork' => [
                'Kerjasama dengan Tim',
                'Komunikasi dengan Rekan Kerja',
                'Membantu Rekan Saat Dibutuhkan',
                'Menghargai Pendapat Orang Lain',
                'Kemampuan Beradaptasi dalam Tim',
            ],
            'Kinerja' => [
                'Kualitas Hasil Kerja',
                'Kecepatan Menyelesaikan Tugas',
                'Ketelitian dalam Bekerja',
                'Produktivitas Kerja',
                'Efisiensi Kerja',
            ],
            'Inisiatif & Tanggung Jawab' => [
                'Inisiatif Mengambil Tugas',
                'Tanggung Jawab terhadap Pekerjaan',
                'Kemauan Belajar Hal Baru',
                'Kemampuan Menyelesaikan Masalah',
                'Kemandirian dalam Bekerja',
            ],
            'Sikap Profesional' => [
                'Sikap terhadap Atasan',
                'Sikap terhadap Rekan Kerja',
                'Etika dalam Bekerja',
                'Kemampuan Mengendalikan Emosi',
                'Penampilan dan Profesionalisme',
            ],
        ];

        foreach ($data as $indicatorName => $descriptions) {
            $indicator = EvaluationIndicator::create(['name' => $indicatorName]);

            foreach ($descriptions as $descName) {
                $indicator->descriptions()->create([
                    'name' => $descName
                ]);
            }
        }
    }
}