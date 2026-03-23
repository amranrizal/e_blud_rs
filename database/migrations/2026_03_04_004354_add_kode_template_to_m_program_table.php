<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_program', function (Blueprint $table) {
            $table->string('kode_template', 50)
                  ->nullable()
                  ->after('kode_program')
                  ->comment('Kode template nasional format X.XX.xx.xx.xxx');
        });
    }

    public function down(): void
    {
        Schema::table('m_program', function (Blueprint $table) {
            $table->dropColumn('kode_template');
        });
    }
};