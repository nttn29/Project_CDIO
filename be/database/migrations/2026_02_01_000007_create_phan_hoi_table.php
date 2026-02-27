<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phan_hoi', function (Blueprint $table) {
            $table->bigIncrements('id_phan_hoi');
            $table->unsignedBigInteger('id_yeu_cau');
            $table->unsignedBigInteger('id_cu_dan');
            $table->tinyInteger('danh_gia')->nullable();
            $table->text('binh_luan')->nullable();
            $table->timestamps();

            $table->foreign('id_yeu_cau')->references('id_yeu_cau')->on('yeu_cau_bao_tri')->onDelete('cascade');
            $table->foreign('id_cu_dan')->references('id_nguoi_dung')->on('nguoi_dung')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phan_hoi');
    }
};
