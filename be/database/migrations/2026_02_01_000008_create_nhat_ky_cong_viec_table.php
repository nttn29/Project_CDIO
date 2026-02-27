<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nhat_ky_cong_viec', function (Blueprint $table) {
            $table->bigIncrements('id_nhat_ky');
            $table->unsignedBigInteger('id_phan_cong');
            $table->text('mo_ta_cong_viec')->nullable();
            $table->decimal('so_gio_lam', 8, 2)->nullable();
            $table->decimal('chi_phi', 10, 2)->nullable();
            $table->date('ngay_nhat_ky')->nullable();
            $table->timestamps();

            $table->foreign('id_phan_cong')->references('id_phan_cong')->on('phan_cong')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nhat_ky_cong_viec');
    }
};
