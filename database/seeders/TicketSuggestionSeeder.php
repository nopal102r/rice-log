<?php

namespace Database\Seeders;

use App\Models\TicketCategory;
use App\Models\TicketSuggestion;
use Illuminate\Database\Seeder;

class TicketSuggestionSeeder extends Seeder
{
    public function run(): void
    {
        $suggestions = [
            'technical' => [
                'Mohon maaf atas kendalanya. Silakan coba hapus cache browser atau gunakan mode incognito.',
                'Masalah ini sedang diselidiki oleh tim pengembang kami. Harap tunggu 1x24 jam.',
            ],
            'attendance' => [
                'Koreksi absensi telah diverifikasi dengan data GPS. Perubahan akan muncul besok pagi.',
                'Harap lampirkan bukti foto jika Anda melakukan absen di luar radius kantor.',
            ],
            'payroll' => [
                'Perhitungan gaji disesuaikan dengan jumlah setoran beras. Silakan cek riwayat setoran Anda.',
                'Kekurangan pembayaran akan ditambahkan pada periode penggajian berikutnya.',
            ]
        ];

        foreach ($suggestions as $slug => $texts) {
            $category = TicketCategory::where('slug', $slug)->first();
            if ($category) {
                foreach ($texts as $text) {
                    TicketSuggestion::updateOrCreate(['category_id' => $category->id, 'text' => $text]);
                }
            }
        }
    }
}
