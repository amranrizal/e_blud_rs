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
       Schema::create('sbu_parameters', function (Blueprint $table) {
    $table->id();

    $table->foreignId('standar_harga_id')
        ->constrained('standar_hargas')
        ->cascadeOnDelete();

    $table->string('nama_parameter');   // contoh: orang, hari, jam
    $table->decimal('nilai_default', 10, 2)->nullable();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sbu_parameters');
    }
};
