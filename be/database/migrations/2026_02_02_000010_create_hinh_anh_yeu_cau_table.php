<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hinh_anh_yeu_cau', function (Blueprint $table) {
            $table->bigIncrements('id_hinh_anh');
            $table->unsignedBigInteger('id_yeu_cau');
            $table->string('duong_dan_file');
            $table->string('ten_file')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('kich_thuoc')->nullable(); // in bytes
            $table->timestamps();

            $table->foreign('id_yeu_cau')->references('id_yeu_cau')->on('yeu_cau_bao_tri')->onDelete('cascade');
            $table->index('id_yeu_cau');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hinh_anh_yeu_cau');
    }
};
