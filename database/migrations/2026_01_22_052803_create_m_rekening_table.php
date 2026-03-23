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
        Schema::create('m_rekening', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('parent_id')->nullable();
        $table->string('kode_akun'); // Contoh: 5.1.02.01.01.0001
        $table->string('nama_akun'); // Contoh: Belanja Alat Tulis Kantor
        // Hirarki 6 Level Akun (Permendagri 90)
        $table->enum('level', [
            'Akun',             // Level 1 (5)
            'Kelompok',         // Level 2 (5.1)
            'Jenis',            // Level 3 (5.1.02)
            'Objek',            // Level 4 (5.1.02.01)
            'Rincian Objek',    // Level 5 (5.1.02.01.01)
            'Sub Rincian Objek' // Level 6 (5.1.02.01.01.0001) -> Ini yang dipakai belanja
        ]);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_rekening');
    }
};
