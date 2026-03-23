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
            Schema::create('asb_parameters', function (Blueprint $table) {
        $table->id();

        $table->foreignId('asb_id')
            ->constrained('asb')
            ->cascadeOnDelete();

        $table->string('nama_parameter');   // peserta, hari, dll
        $table->decimal('nilai_default', 10, 2)->nullable();

        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asb_parameters');
    }
};
