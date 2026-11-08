<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->decimal('starting_cash', 15, 2)->default(0); // Modal awal kasir
            $table->decimal('expected_cash', 15, 2)->nullable(); // Estimasi dari transaksi kas
            $table->decimal('actual_cash', 15, 2)->nullable(); // Hitungan fisik kasir
            $table->decimal('difference', 15, 2)->nullable(); // Selisih lebih/kurang
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
