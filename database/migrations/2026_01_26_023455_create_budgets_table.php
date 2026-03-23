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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();

            // 1. Identitas Unit
            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('m_unit_kerja')->onDelete('cascade');

            // 2. Kegiatan
            $table->unsignedBigInteger('sub_kegiatan_id');
            $table->foreign('sub_kegiatan_id')->references('id')->on('m_program')->onDelete('cascade');

            // 3. Rekening Belanja (COA) - KITA LEPAS FOREIGN KEY-NYA
            $table->string('kode_akun')->index(); 
            
            // HAPUS ATAU KOMENTARI BARIS INI:
            // $table->foreign('kode_akun')->references('kode_akun')... 
            
            // 4. Detail Barang
            $table->string('uraian'); 
            $table->decimal('harga_satuan', 19, 2);
            $table->decimal('volume', 10, 2);
            $table->string('satuan');
            
            // 5. Total
            $table->decimal('total_anggaran', 19, 2);

            $table->year('tahun');
            $table->enum('status', ['draft', 'diajukan', 'disahkan'])->default('draft');

            $table->timestamps();
        });
    }
};
