<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('renja', function (Blueprint $table) {

            $table->index(['unit_id','tahun']);
            $table->index('sub_kegiatan_id');

        });
    }

    public function down()
    {
        Schema::table('renja', function (Blueprint $table) {

            $table->dropIndex(['unit_id','tahun']);
            $table->dropIndex(['sub_kegiatan_id']);

        });
    }
};