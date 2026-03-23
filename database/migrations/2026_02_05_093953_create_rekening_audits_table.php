<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rekening_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rekening_id');
            $table->string('action'); // create, update, delete
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rekening_audits');
    }
};
