<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Masalah Teknis (Aplikasi)', 'slug' => 'technical'],
            ['name' => 'Koreksi Absensi', 'slug' => 'attendance'],
            ['name' => 'Keluhan Fasilitas', 'slug' => 'facility'],
            ['name' => 'Masalah Penggajian', 'slug' => 'payroll'],
            ['name' => 'Lainnya', 'slug' => 'others'],
        ];

        foreach ($categories as $category) {
            \App\Models\TicketCategory::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
