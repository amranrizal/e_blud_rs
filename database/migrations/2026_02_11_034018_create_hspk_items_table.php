<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hspk_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('hspk_id')
                ->constrained('hspk')
                ->cascadeOnDelete();

            $table->foreignId('standar_harga_id')
                ->constrained('standar_hargas')
                ->restrictOnDelete();

            $table->decimal('koefisien', 12, 4);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hspk_items');
    }
};
