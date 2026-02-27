<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phan_cong', function (Blueprint $table) {
            $table->bigIncrements('id_phan_cong');
            $table->unsignedBigInteger('id_yeu_cau');
            $table->unsignedBigInteger('id_ky_thuat_vien')->nullable();
            $table->date('ngay_phan_cong')->nullable();
            $table->time('gio_hen')->nullable();
            $table->string('trang_thai')->default('mo');
            $table->timestamps();

            $table->foreign('id_yeu_cau')->references('id_yeu_cau')->on('yeu_cau_bao_tri')->onDelete('cascade');
            $table->foreign('id_ky_thuat_vien')->references('id_nguoi_dung')->on('nguoi_dung')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phan_cong');
    }
};
