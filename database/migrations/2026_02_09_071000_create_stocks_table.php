<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // gabah, beras_giling, packed_5kg, etc.
            $table->decimal('quantity', 12, 2)->default(0);
            $table->string('unit')->default('kg'); // kg or karung
            $table->timestamps();
        });

        // Initialize default stocks
        DB::table('stocks')->insert([
            ['name' => 'gabah', 'quantity' => 0, 'unit' => 'kg', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'beras_giling', 'quantity' => 0, 'unit' => 'kg', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'packed_5kg', 'quantity' => 0, 'unit' => 'karung', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'packed_10kg', 'quantity' => 0, 'unit' => 'karung', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'packed_15kg', 'quantity' => 0, 'unit' => 'karung', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'packed_20kg', 'quantity' => 0, 'unit' => 'karung', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'packed_25kg', 'quantity' => 0, 'unit' => 'karung', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
