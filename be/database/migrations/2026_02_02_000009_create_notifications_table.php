<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id_notification');
            $table->unsignedBigInteger('id_yeu_cau')->nullable();
            $table->unsignedBigInteger('id_cu_dan')->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('id_yeu_cau')->references('id_yeu_cau')->on('yeu_cau_bao_tri')->onDelete('set null');
            $table->foreign('id_cu_dan')->references('id_nguoi_dung')->on('nguoi_dung')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
