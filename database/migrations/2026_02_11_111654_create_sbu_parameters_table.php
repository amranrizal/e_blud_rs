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

    $table->string('kode_parameter');     // contoh: orang, hari, jam
    $table->string('label');              // contoh: Jumlah Peserta
    $table->string('tipe')->default('numeric'); // numeric, integer
    $table->decimal('nilai_default', 10, 2)->nullable();

    $table->boolean('is_required')->default(true);
    $table->integer('urutan')->default(1);

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
