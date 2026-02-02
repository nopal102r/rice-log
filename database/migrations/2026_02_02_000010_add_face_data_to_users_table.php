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
        Schema::table('users', function (Blueprint $table) {
            // Store face descriptors as JSON (array of numbers from face-api)
            $table->json('face_data')->nullable()->after('job');
            // Store face enrollment status
            $table->boolean('face_enrolled')->default(false)->after('face_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['face_data', 'face_enrolled']);
        });
    }
};
