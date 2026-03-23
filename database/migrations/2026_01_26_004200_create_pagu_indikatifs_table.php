<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_pagu_indikatifs_table.php

public function up()
    {
        Schema::create('pagu_indikatifs', function (Blueprint $table) {
            $table->id();
            
            // --- RELASI KE UNIT KERJA (Pake ID Integer) ---
            $table->unsignedBigInteger('unit_id'); // KEMBALI KE INTEGER
            
            $table->foreign('unit_id')
                ->references('id')        // Referensi ke kolom 'id' di tabel m_unit_kerja
                ->on('m_unit_kerja')
                ->onDelete('cascade');

            // Relasi ke Program
            $table->unsignedBigInteger('sub_kegiatan_id');
            $table->foreign('sub_kegiatan_id')->references('id')->on('m_program')->onDelete('cascade');

            $table->decimal('pagu', 19, 2)->default(0);
            $table->year('tahun');

            $table->unique(['unit_id', 'sub_kegiatan_id', 'tahun'], 'unique_pagu_unit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagu_indikatifs');
    }
};
