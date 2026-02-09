<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update Users Table (Job Enum)
        // Step 1: Convert ENUM to VARCHAR temporarily to allow data updates
        DB::statement("ALTER TABLE users MODIFY COLUMN job VARCHAR(20) NULL");
        
        // Step 2: Update existing data to match new enum values
        DB::statement("UPDATE users SET job = 'supir' WHERE job = 'kurir'");
        DB::statement("UPDATE users SET job = 'petani' WHERE job = 'sawah'");
        
        // Step 3: Convert back to ENUM with new values
        DB::statement("ALTER TABLE users MODIFY COLUMN job ENUM('supir', 'petani', 'ngegiling', 'sales') NULL");

        // 2. Update Payroll Settings Table
        Schema::table('payroll_settings', function (Blueprint $table) {
            $table->decimal('driver_rate_per_kg', 10, 2)->default(0)->after('price_per_kg');
            $table->decimal('farmer_rate_per_box', 10, 2)->default(0)->after('driver_rate_per_kg');
            $table->decimal('miller_rate_per_kg', 10, 2)->default(0)->after('farmer_rate_per_box');
            $table->decimal('sales_commission_rate', 5, 2)->default(0)->after('miller_rate_per_kg'); // In percentage or fixed? Let's assume fixed or handled in logic, but standard commission is usually percentage. Plan said "sales_commission_rate".
        });

        // 3. Update Deposits Table
        Schema::table('deposits', function (Blueprint $table) {
            $table->enum('type', ['regular', 'land_management'])->default('regular')->after('user_id');
            $table->integer('box_count')->nullable()->after('type');
            $table->decimal('money_amount', 12, 2)->nullable()->after('box_count'); // For Sales Revenue
            $table->decimal('wage_amount', 12, 2)->default(0)->after('total_price'); // Employee's cut
            $table->time('start_time')->nullable()->after('notes');
            $table->time('end_time')->nullable()->after('start_time');
            
            // Make weight nullable as land management doesn't use it
            $table->decimal('weight', 8, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse Deposits
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn(['type', 'box_count', 'money_amount', 'wage_amount', 'start_time', 'end_time']);
            $table->decimal('weight', 8, 2)->nullable(false)->change();
        });

        // Reverse Payroll Settings
        Schema::table('payroll_settings', function (Blueprint $table) {
            $table->dropColumn(['driver_rate_per_kg', 'farmer_rate_per_box', 'miller_rate_per_kg', 'sales_commission_rate']);
        });

        // Reverse Users (Revert to original ENUM)
        // Original: 'kurir', 'sawah', 'ngegiling'
        DB::statement("ALTER TABLE users MODIFY COLUMN job ENUM('kurir', 'sawah', 'ngegiling') NULL");
    }
};
