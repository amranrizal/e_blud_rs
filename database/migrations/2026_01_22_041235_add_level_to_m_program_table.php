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
        Schema::create('m_program', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('parent_id')->nullable();
        $table->string('kode_program'); // Menyimpan Kode Rekening (X.XX.XX...)
        $table->string('nama_program'); // Menyimpan Nama Nomenklatur
        // Hirarki 5 Level SIPD
        $table->enum('level', [
            'Urusan', 
            'Bidang Urusan', 
            'Program', 
            'Kegiatan', 
            'Sub Kegiatan'
        ]);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_program', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
};
