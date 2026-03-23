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
        Schema::create('t_pendapatan', function (Blueprint $table) {
            $table->id();
            
            // 1. Identitas
            $table->unsignedBigInteger('unit_id')->nullable(); // Biasanya Pendapatan terpusat, tapi bisa per unit
            $table->year('tahun');

            // 2. Kode Akun (Harus Akun Pendapatan / Kepala 4)
            $table->string('kode_akun')->index(); 
            
            // 3. Detail
            $table->string('uraian'); // Contoh: Pendapatan Jasa Layanan Pasien Umum
            
            // 4. Rincian Perhitungan (Opsional, tapi bagus untuk RBA)
            $table->decimal('volume', 10, 2)->default(0); // Misal: 1000 Pasien
            $table->string('satuan')->nullable();         // Misal: Orang/Tahun
            $table->decimal('tarif', 19, 2)->default(0);  // Misal: Rp 50.000
            
            // 5. Total Target
            $table->decimal('jumlah', 19, 2); // Volume x Tarif = Total Target
            
            $table->timestamps();

            // Relasi (Opsional)
            // $table->foreign('unit_id')->references('id')->on('m_unit_kerja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_pendapatan');
    }
};