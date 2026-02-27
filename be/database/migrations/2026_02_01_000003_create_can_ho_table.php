<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('can_ho', function (Blueprint $table) {
            $table->bigIncrements('id_can_ho');
            $table->unsignedBigInteger('id_toa_nha');
            $table->string('so_can_ho')->nullable();
            $table->integer('tang')->nullable();
            $table->unsignedBigInteger('id_cu_dan')->nullable();
            $table->timestamps();

            $table->foreign('id_toa_nha')->references('id_toa_nha')->on('toa_nha')->onDelete('cascade');
            $table->foreign('id_cu_dan')->references('id_nguoi_dung')->on('nguoi_dung')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('can_ho');
    }
};
