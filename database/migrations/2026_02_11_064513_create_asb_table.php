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
        Schema::create('asb', function (Blueprint $table) {
            $table->id();

            $table->string('kode')->unique();      // contoh: ASB-001
            $table->string('uraian');              // Nama ASB
            $table->string('satuan');              // orang, hari, paket, dll

            $table->decimal('tarif', 15, 2);       // Nilai standar per satuan

            $table->foreignId('rekening_id')
                ->constrained('m_rekening')
                ->restrictOnDelete();

            $table->year('tahun');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asb');
    }
};
