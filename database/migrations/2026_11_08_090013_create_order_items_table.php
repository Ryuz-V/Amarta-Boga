<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->string('menu_name'); // Snapshot nama menu
            $table->decimal('unit_price', 15, 2); // Harga dasar per item
            $table->decimal('cost_price', 15, 2)->default(0); // Snapshot HPP untuk laba rugi
            $table->integer('quantity')->default(1);
            $table->decimal('modifiers_price', 15, 2)->default(0); // Total tambahan varian & addon
            $table->decimal('subtotal', 15, 2); // (unit_price + modifiers_price) * quantity
            $table->text('notes')->nullable(); // Catatan khusus (contoh: "Tanpa Bawang", "Sangat Pedas")
            $table->enum('status', ['pending', 'cooking', 'ready', 'served', 'cancelled'])->default('pending');
            $table->text('cancel_reason')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
