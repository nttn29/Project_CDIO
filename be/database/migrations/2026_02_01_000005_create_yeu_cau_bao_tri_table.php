<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('yeu_cau_bao_tri', function (Blueprint $table) {
            $table->bigIncrements('id_yeu_cau');
            $table->unsignedBigInteger('id_cu_dan');
            $table->unsignedBigInteger('id_can_ho')->nullable();
            $table->unsignedBigInteger('id_loai_su_co')->nullable();
            $table->text('mo_ta')->nullable();
            $table->string('thoi_gian_uu_tien')->nullable();
            $table->string('trang_thai')->default('moi');
            $table->timestamps();

            $table->foreign('id_cu_dan')->references('id_nguoi_dung')->on('nguoi_dung')->onDelete('cascade');
            $table->foreign('id_can_ho')->references('id_can_ho')->on('can_ho')->onDelete('set null');
            $table->foreign('id_loai_su_co')->references('id_loai_su_co')->on('loai_su_co')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('yeu_cau_bao_tri');
    }
};
