<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payroll_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('price_per_kg', 10, 2); // Harga per kg beras (misalnya 30000)
            $table->decimal('office_latitude', 11, 8); // Latitude kantor pusat
            $table->decimal('office_longitude', 11, 8); // Longitude kantor pusat
            $table->decimal('max_distance_allowed', 8, 2)->default(2.0); // Max jarak dari kantor (km)
            $table->integer('leave_days_per_month')->default(3); // Jumlah hari cuti per bulan
            $table->integer('min_deposit_per_week')->default(1); // Min deposit per minggu
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_settings');
    }
};
