<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Absence;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employees = User::where('role', 'karyawan')->get();
        if ($employees->isEmpty()) {
            return;
        }

        $startDate = Carbon::now()->subYear();
        $endDate = Carbon::now();

        // 80% hadir, 10% sakit, 5% izin, 5% alpa (no entry)
        for ($date = clone $startDate; $date->lte($endDate); $date->addDay()) {
            // Skip sundays
            if ($date->isSunday()) {
                continue;
            }

            foreach ($employees as $emp) {
                $rand = rand(1, 100);

                if ($rand <= 80) {
                    $status = 'hadir';
                } elseif ($rand <= 90) {
                    $status = 'sakit';
                } elseif ($rand <= 95) {
                    $status = 'izin';
                } else {
                    continue; // Alpa (no record)
                }

                if ($status === 'hadir') {
                    // Check in between 07:00 and 09:00
                    $checkInTime = clone $date;
                    $checkInTime->setTime(rand(7, 8), rand(0, 59), rand(0, 59));
                    
                    Absence::create([
                        'user_id' => $emp->id,
                        'type' => 'masuk',
                        'status' => 'hadir',
                        'latitude' => -6.2088 + (mt_rand(-10, 10) / 10000),
                        'longitude' => 106.8456 + (mt_rand(-10, 10) / 10000),
                        'distance_from_office' => rand(1, 19) / 10,
                        'created_at' => $checkInTime,
                        'updated_at' => $checkInTime,
                    ]);

                    // Check out between 16:00 and 18:00
                    $checkOutTime = clone $date;
                    $checkOutTime->setTime(rand(16, 17), rand(0, 59), rand(0, 59));
                    
                    Absence::create([
                        'user_id' => $emp->id,
                        'type' => 'keluar',
                        'status' => 'hadir',
                        'latitude' => -6.2088 + (mt_rand(-10, 10) / 10000),
                        'longitude' => 106.8456 + (mt_rand(-10, 10) / 10000),
                        'distance_from_office' => rand(1, 19) / 10,
                        'created_at' => $checkOutTime,
                        'updated_at' => $checkOutTime,
                    ]);
                } else {
                    // Sakit or Izin usually just check in once indicating status, or has an entry
                    $absenceTime = clone $date;
                    $absenceTime->setTime(rand(7, 9), rand(0, 59), rand(0, 59));
                    
                    Absence::create([
                        'user_id' => $emp->id,
                        'type' => 'masuk',
                        'status' => $status,
                        'description' => $status === 'sakit' ? 'Surat dokter terlampir' : 'Keperluan keluarga',
                        'latitude' => -6.2088 + (mt_rand(-100, 100) / 10000),
                        'longitude' => 106.8456 + (mt_rand(-100, 100) / 10000),
                        'distance_from_office' => rand(50, 150) / 10, // Far from office
                        'created_at' => $absenceTime,
                        'updated_at' => $absenceTime,
                    ]);
                }
            }
        }
    }
}
