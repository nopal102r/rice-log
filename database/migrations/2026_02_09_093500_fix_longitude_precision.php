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
        // Use raw SQL to avoid doctrine/dbal dependency issues usually required for ->change()
        // Longitude requires 3 digits before decimal (up to 180), so (11, 8)
        DB::statement('ALTER TABLE absences MODIFY longitude DECIMAL(11, 8) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE absences MODIFY longitude DECIMAL(10, 8) NULL');
    }
};
