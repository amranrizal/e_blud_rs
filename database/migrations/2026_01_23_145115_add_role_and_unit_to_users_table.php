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
        // Kolom Role: admin, verifikator, user
        $table->string('role', 20)->default('user')->after('email');
        
        // Relasi ke Unit Kerja (Nullable karena Admin mungkin tidak punya unit spesifik)
        $table->unsignedBigInteger('id_unit_kerja')->nullable()->after('role');
        
        // Indexing untuk performa
        $table->index('id_unit_kerja');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['role', 'id_unit_kerja']);
    });
}
};
