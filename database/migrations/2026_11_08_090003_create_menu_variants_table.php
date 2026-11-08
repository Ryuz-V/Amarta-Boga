<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->string('group_name')->default('Ukuran'); // e.g. Ukuran, Level Pedas, Es
            $table->string('name'); // e.g. Regular, Jumbo, Level 1, Less Ice
            $table->decimal('price_adjustment', 15, 2)->default(0); // Tambahan harga
            $table->boolean('is_available')->default(true);
            $table->timestamps();

            $table->index(['menu_id', 'group_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_variants');
    }
};
