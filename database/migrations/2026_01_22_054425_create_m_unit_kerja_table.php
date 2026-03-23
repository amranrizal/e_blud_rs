<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kita pakai nama 'm_unit_kerja' biar seragam dengan m_program & m_rekening
        Schema::create('m_unit_kerja', function (Blueprint $table) {
            $table->id();
            $table->string('kode_unit')->unique(); // Contoh: 1.02.01
            $table->string('nama_unit');           // Contoh: RSUD / Poli Bedah
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_unit_kerja');
    }
};