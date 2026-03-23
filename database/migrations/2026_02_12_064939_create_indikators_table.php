<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indikators', function (Blueprint $table) {
            $table->id();

            // Relasi ke m_program (Program/Kegiatan/Sub Kegiatan)
            $table->unsignedBigInteger('m_program_id');

            // Jenis indikator (dibatasi di level aplikasi)
            $table->enum('jenis', ['outcome', 'output']);

            $table->text('tolok_ukur');
            $table->decimal('target', 15, 2);
            $table->string('satuan', 100);

            $table->year('tahun');

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            // Foreign key ke m_program
            $table->foreign('m_program_id')
                ->references('id')
                ->on('m_program')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indikators');
    }
};
