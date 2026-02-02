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
        Schema::create('monthly_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('year');
            $table->integer('month');
            $table->integer('days_present')->default(0); // Hari hadir
            $table->integer('days_sick')->default(0); // Hari sakit
            $table->integer('days_leave')->default(0); // Hari izin
            $table->integer('leave_approved')->default(0); // Hari cuti yg disetujui
            $table->decimal('total_kg_deposited', 8, 2)->default(0); // Total kg setor
            $table->decimal('total_salary', 12, 2)->default(0); // Total gaji
            $table->enum('status', ['active', 'inactive'])->default('active'); // Karyawan aktif atau tidak
            $table->timestamps();

            // Unique constraint untuk user per bulan
            $table->unique(['user_id', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_summaries');
    }
};
