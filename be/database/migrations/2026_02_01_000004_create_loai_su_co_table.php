<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loai_su_co', function (Blueprint $table) {
            $table->bigIncrements('id_loai_su_co');
            $table->string('ten_loai')->nullable();
            $table->integer('muc_uu_tien')->default(0);
            $table->text('mo_ta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loai_su_co');
    }
};
