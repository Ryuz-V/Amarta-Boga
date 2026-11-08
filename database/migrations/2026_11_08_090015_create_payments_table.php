<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Kasir penerima
            $table->enum('payment_method', ['cash', 'qris', 'card', 'transfer', 'ewallet'])->default('cash');
            $table->decimal('amount', 15, 2);
            $table->string('reference_number')->nullable(); // No kartu / trace / QRIS ref
            $table->enum('status', ['success', 'pending', 'failed', 'refunded'])->default('success');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
