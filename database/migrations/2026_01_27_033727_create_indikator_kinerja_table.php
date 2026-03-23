<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('indikator_kinerja', function (Blueprint $table) {
            $table->id();

            // 1. Buat kolomnya dulu (Pastikan tipe data Unsigned Big Integer)
            $table->unsignedBigInteger('pagu_indikatif_id');

            // 2. Kolom data lainnya
            $table->string('jenis'); 
            $table->text('tolok_ukur'); 
            $table->string('target'); 
            $table->string('satuan'); 
            $table->timestamps();

            // 3. Definisikan Foreign Key secara manual di bawah
            // PENTING: Pastikan 'blud_pagu_indikatif' sesuai dengan nama tabel fisik di database Boss.
            $table->foreign('pagu_indikatif_id')
                  ->references('id')
                  ->on('pagu_indikatifs') // <--- CEK NAMA TABEL INI
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('indikator_kinerja');
    }
};
