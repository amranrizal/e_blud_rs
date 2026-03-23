<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('renja', function (Blueprint $table) {

            $table->id();

            // relasi ke unit kerja
            $table->unsignedBigInteger('unit_id');

            // relasi ke sub kegiatan SIPD
            $table->unsignedBigInteger('sub_kegiatan_id');

            // tahun anggaran
            $table->integer('tahun');

            // indikator kinerja
            $table->text('indikator_kinerja')->nullable();

            // target kegiatan
            $table->string('target')->nullable();

            // satuan target
            $table->string('satuan')->nullable();

            // pagu rencana kegiatan
            $table->decimal('pagu_rencana',18,2)->default(0);

            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('renja');
    }
};