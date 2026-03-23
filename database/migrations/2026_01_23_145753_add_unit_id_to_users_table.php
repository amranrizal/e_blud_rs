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
        // 1. Tambah Kolom Role (Penting untuk hak akses)
      //  $table->string('role', 20)->default('user')->after('email'); // admin, verifikator, user

        // 2. Tambah Kolom id_unit_kerja (FK)
        // Tipe data harus sama persis dengan 'id' di gambar Bossku (bigint unsigned)
        if (!Schema::hasColumn('users', 'id_unit_kerja')) {
        $table->unsignedBigInteger('id_unit_kerja')->nullable()->after('role');

        // 3. Bikin Kunci Asing (Foreign Key)
        // Ganti 'unit_kerja' dengan nama tabel asli yang ada di gambar Bossku
        $table->foreign('id_unit_kerja')->references('id')->on('unit_kerja')->onDelete('set null');
        }
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        // Hapus foreign key dulu baru kolomnya
      if (Schema::hasColumn('users', 'id_unit_kerja')) {  
        $table->dropForeign(['id_unit_kerja']);
        $table->dropColumn(['role', 'id_unit_kerja']);
      }
    });
}
};
