<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['in', 'out', 'adjustment', 'waste', 'order_deduction']);
            $table->decimal('quantity', 14, 2); // Nilai mutlak/perubahan
            $table->decimal('before_stock', 14, 2)->default(0);
            $table->decimal('after_stock', 14, 2)->default(0);
            $table->decimal('cost_per_unit', 15, 2)->nullable();
            $table->string('reference_type')->nullable(); // Order, StockOpname, dll.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['ingredient_id', 'type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
