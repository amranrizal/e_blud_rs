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
        Schema::table('pagu_indikatifs', function (Blueprint $table) {
            $table->string('status_validasi')->default('draft')->after('tahun');
            $table->text('catatan_revisi')->nullable()->after('status_validasi');
            $table->unsignedBigInteger('validator_id')->nullable()->after('catatan_revisi');
            $table->dateTime('tgl_validasi')->nullable()->after('validator_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagu_indikatifs', function (Blueprint $table) {
            //
        });
    }
};
