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
        
        Schema::table('standar_hargas', function (Blueprint $table) {

        // 1️⃣ Tambah kolom dulu
        $table->unsignedBigInteger('rekening_id')->nullable()->after('kode_kelompok');

        // 2️⃣ Baru pasang foreign key
        $table->foreign('rekening_id')
              ->references('id')
              ->on('m_rekening')
              ->nullOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('standar_hargas', function (Blueprint $table) {
            //
        });
    }
};
