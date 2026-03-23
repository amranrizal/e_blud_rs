<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('budgets', function (Blueprint $table) {

            // Relasi ke tabel standar_hargas
            $table->unsignedBigInteger('standar_harga_id')
                  ->nullable()
                  ->after('kode_akun');

            // Flag manual / SSH
            $table->boolean('is_manual')
                  ->default(false)
                  ->after('standar_harga_id');

            // Optional: Foreign key (disarankan)
            $table->foreign('standar_harga_id')
                  ->references('id')
                  ->on('standar_hargas')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {

            $table->dropForeign(['standar_harga_id']);
            $table->dropColumn(['standar_harga_id', 'is_manual']);
        });
    }
};

