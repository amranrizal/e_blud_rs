<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('standar_hargas', function (Blueprint $table) {

            if (!Schema::hasColumn('standar_hargas', 'uraian_kelompok')) {
                $table->string('uraian_kelompok')->nullable()->after('kode_kelompok');
            }

            if (!Schema::hasColumn('standar_hargas', 'id_standar_harga')) {
                $table->bigInteger('id_standar_harga')->nullable()->after('uraian_kelompok');
            }

            if (!Schema::hasColumn('standar_hargas', 'kode_rekening')) {
                $table->text('kode_rekening')->nullable()->after('harga');
            }

        });
    }

    public function down(): void
    {
        Schema::table('standar_hargas', function (Blueprint $table) {

            $table->dropColumn([
                'uraian_kelompok',
                'id_standar_harga',
                'kode_rekening'
            ]);

        });
    }
};