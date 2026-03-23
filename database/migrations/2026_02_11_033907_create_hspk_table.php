<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hspk', function (Blueprint $table) {
            $table->id();

            $table->string('uraian');                // Nama pekerjaan
            $table->string('satuan');                // m2, m3, unit, dll
            $table->decimal('harga_total', 15, 2)->default(0);

            $table->foreignId('rekening_id')
                ->constrained('m_rekening')
                ->restrictOnDelete();

            $table->year('tahun');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hspk');
    }
};
