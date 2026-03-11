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
        Schema::create('evaluation_indicators', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('evaluation_descriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_indicator_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('boss_id')->constrained('users')->onDelete('cascade');
            $table->integer('month');
            $table->integer('year');
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'month', 'year']);
        });

        Schema::create('evaluation_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluation_description_id')->constrained()->onDelete('cascade');
            $table->integer('rating'); // 1-5
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_ratings');
        Schema::dropIfExists('evaluations');
        Schema::dropIfExists('evaluation_descriptions');
        Schema::dropIfExists('evaluation_indicators');
    }
};
