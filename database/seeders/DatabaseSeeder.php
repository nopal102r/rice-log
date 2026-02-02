<?php

namespace Database\Seeders;

use App\Models\PayrollSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Boss/Manager
        User::factory()->create([
            'name' => 'Bos Pabrik',
            'email' => 'bos@ricemail.com',
            'role' => 'bos',
            'phone' => '081234567890',
            'address' => 'Jl. Pabrik No. 1',
            'status' => 'active',
        ]);

        // Create sample employees
        User::factory(10)->create([
            'role' => 'karyawan',
            'job' => fake()->randomElement(['kurir', 'sawah', 'ngegiling']),
            'status' => 'active',
        ]);

        // Create Payroll Settings
        PayrollSetting::create([
            'price_per_kg' => 30000, // Rp 30.000 per kg
            'office_latitude' => -6.2088,
            'office_longitude' => 106.8456,
            'max_distance_allowed' => 2.0,
            'leave_days_per_month' => 3,
            'min_deposit_per_week' => 1,
        ]);
    }
}
