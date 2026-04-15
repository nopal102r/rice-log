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
        Schema::create('point_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_name');
            $table->string('target_role')->nullable(); // e.g., 'employee', 'boss'
            $table->string('condition_operator'); // e.g., '<', '>', 'BETWEEN'
            $table->string('condition_value'); // e.g., '06:30' or '15'
            $table->integer('point_modifier');
            $table->timestamps();
        });

        Schema::create('point_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_type'); // 'EARN', 'SPEND', 'PENALTY'
            $table->integer('amount');
            $table->integer('current_balance');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('flexibility_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->integer('point_cost');
            $table->integer('stock_limit')->nullable();
            $table->timestamps();
        });

        Schema::create('user_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('flexibility_items')->cascadeOnDelete();
            $table->string('status')->default('AVAILABLE'); // 'AVAILABLE', 'USED', 'EXPIRED'
            $table->unsignedBigInteger('used_at_absence_id')->nullable();
            $table->foreign('used_at_absence_id')->references('id')->on('absences')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_tokens');
        Schema::dropIfExists('flexibility_items');
        Schema::dropIfExists('point_ledgers');
        Schema::dropIfExists('point_rules');
    }
};
