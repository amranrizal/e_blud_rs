<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('t_pembiayaan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id');
            $table->string('tahun', 4); // Contoh: 2024
            $table->string('kode_akun', 20); // Foreign key ke m_rekening (Induk Akun 6)
            $table->string('uraian'); // Keterangan detail
            $table->decimal('volume', 15, 2)->default(0);
            $table->string('satuan', 50)->nullable();
            $table->decimal('harga_satuan', 19, 2)->default(0); // Atau Tarif/Nominal
            $table->decimal('jumlah', 19, 2)->default(0); // volume * harga_satuan
            $table->timestamps();

            // Indexing untuk performa
            $table->index(['unit_id', 'tahun', 'kode_akun']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_pembiayaan');
    }
};
