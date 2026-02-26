<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('ky_thuat_vien', function (Blueprint $table) {
            $table->id();
            $table->string('ten');
            $table->string('so_dien_thoai');
            $table->enum('trang_thai', ['free', 'busy'])->default('free');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ky_thuat_vien');
    }
};
