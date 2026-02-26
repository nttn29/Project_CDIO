<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('technician_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['moi', 'dang_xu_ly', 'hoan_thanh', 'huy'])->default('moi');
            $table->enum('priority', ['thap', 'trung_binh', 'cao'])->default('trung_binh');
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->unsignedBigInteger('technician_id')->nullable();
            $table->timestamps();

            $table->index(['status', 'priority']);
            $table->foreign('technician_id')
                ->references('id_nguoi_dung')
                ->on('nguoi_dung')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technician_jobs');
    }
};
