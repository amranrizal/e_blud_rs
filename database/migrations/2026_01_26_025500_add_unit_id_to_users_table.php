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
        Schema::table('users', function (Blueprint $table) {
            // Kolom relasi ke m_unit_kerja (Boleh null untuk Admin)
            $table->unsignedBigInteger('unit_kerja_id')->nullable()->after('role');
            
            $table->foreign('unit_kerja_id')
                ->references('id')
                ->on('m_unit_kerja')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['unit_kerja_id']);
            $table->dropColumn('unit_kerja_id');
        });
    }
};
