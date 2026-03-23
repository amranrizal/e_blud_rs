<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_standar_hargas_table.php

public function up()
{
    Schema::create('standar_hargas', function (Blueprint $table) {
        $table->id();
        $table->string('kode_kelompok')->comment('SSH, SBU, HSPK, ASB'); 
        $table->string('kode_barang')->nullable()->index();
        $table->text('uraian');
        $table->text('spesifikasi')->nullable();
        $table->string('satuan');
        $table->decimal('harga', 19, 2);
        $table->year('tahun');

        // --- UBAH BAGIAN INI ---
        // Kita buat kolomnya saja, tanpa memaksa foreign key constraint
        // Tambahkan index agar pencarian cepat
        $table->string('kode_akun')->nullable()->index(); 
        
        // Baris $table->foreign(...) KITA HAPUS SAJA AGAR TIDAK ERROR
        // Relasi nanti ditangani lewat Model Laravel

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standar_hargas');
    }
};
