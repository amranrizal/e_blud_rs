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
        Schema::table('standar_hargas', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('tahun');
            $table->boolean('is_group')->default(0)->after('parent_id');

            $table->foreign('parent_id')
                ->references('id')
                ->on('standar_hargas')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('standar_hargas', function (Blueprint $table) {
            //
        });
    }
};
