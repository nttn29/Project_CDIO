<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('chu_can_ho', function (Blueprint $table) {
        $table->id();
        $table->string('ho_ten');
        $table->string('cccd')->unique();
        $table->string('so_dien_thoai');
        $table->string('dia_chi_thuong_tru');
        $table->string('so_nha');
        $table->date('ngay_dang_ky');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chu_can_ho');
    }
};
