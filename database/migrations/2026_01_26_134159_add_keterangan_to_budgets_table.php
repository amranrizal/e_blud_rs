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
    Schema::table('budgets', function (Blueprint $table) {
        // Kolom untuk menampung alasan manual
        $table->text('keterangan_harga')->nullable()->after('satuan'); 
    });
}

public function down()
{
    Schema::table('budgets', function (Blueprint $table) {
        $table->dropColumn('keterangan_harga');
    });
}
};
