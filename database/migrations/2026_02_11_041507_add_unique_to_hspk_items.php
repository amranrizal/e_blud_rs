<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hspk_items', function (Blueprint $table) {
            $table->unique(['hspk_id', 'standar_harga_id'], 'hspk_ssh_unique');
        });
    }

    public function down(): void
    {
        Schema::table('hspk_items', function (Blueprint $table) {
            $table->dropUnique('hspk_ssh_unique');
        });
    }
};
