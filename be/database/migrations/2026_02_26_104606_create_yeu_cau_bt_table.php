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
    Schema::create('yeu_cau_bt', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('id_chu_can_ho');
        $table->text('noi_dung');

        $table->enum('trang_thai', [
            'pending',
            'approved',
            'rejected'
        ])->default('pending');

        $table->timestamps();

        $table->foreign('id_chu_can_ho')
              ->references('id')
              ->on('chu_can_ho')
              ->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yeu_cau_bt');
    }
};
