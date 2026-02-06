<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nguoi_dung', function (Blueprint $table) {
            $table->bigIncrements('id_nguoi_dung');
            $table->string('email')->unique();
            $table->string('ten')->nullable();
            $table->string('mat_khau');
            $table->string('dien_thoai')->nullable();
            $table->string('vai_tro')->nullable();
            $table->string('trang_thai')->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nguoi_dung');
    }
};
