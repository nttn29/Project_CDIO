<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tạo bảng technician_jobs.
 *
 * Bảng này lưu trữ danh sách công việc dành cho ứng dụng kỹ thuật viên.
 * Dữ liệu được đồng bộ tự động từ bảng phan_cong mỗi khi admin giao việc
 * (xem PhanCongController::store), hoặc có thể tạo trực tiếp qua API.
 *
 * Mối liên hệ với các bảng khác:
 *  - technician_id → nguoi_dung.id_nguoi_dung (kỹ thuật viên phụ trách)
 *  - code = "PC-{id_phan_cong}" giúp đồng bộ ngược lại bảng phan_cong
 */
return new class extends Migration
{
    /**
     * Tạo bảng technician_jobs với đầy đủ các cột.
     */
    public function up(): void
    {
        Schema::create('technician_jobs', function (Blueprint $table) {
            $table->id();                                           // Khóa chính tự tăng
            $table->string('code')->unique();                       // Mã công việc duy nhất (ví dụ: "PC-12")
            $table->string('title');                                // Tiêu đề / tên công việc
            $table->string('location')->nullable();                 // Địa điểm thực hiện (ví dụ: "Can ho 101")
            $table->text('description')->nullable();                // Mô tả chi tiết yêu cầu
            $table->enum('status', ['moi', 'dang_xu_ly', 'hoan_thanh', 'huy'])
                  ->default('moi');                                 // Trạng thái công việc
            $table->enum('priority', ['thap', 'trung_binh', 'cao'])
                  ->default('trung_binh');                          // Mức độ ưu tiên
            $table->dateTime('scheduled_at')->nullable();           // Thời điểm dự kiến thực hiện
            $table->dateTime('due_at')->nullable();                 // Hạn chót hoàn thành
            $table->unsignedBigInteger('technician_id')->nullable();// ID kỹ thuật viên phụ trách
            $table->timestamps();                                   // created_at và updated_at

            // Index tổng hợp status + priority giúp tăng tốc truy vấn lọc danh sách
            $table->index(['status', 'priority']);

            // Ràng buộc khóa ngoại: nếu kỹ thuật viên bị xoá → đặt NULL tránh mất công việc
            $table->foreign('technician_id')
                ->references('id_nguoi_dung')
                ->on('nguoi_dung')
                ->nullOnDelete();
        });
    }

    /**
     * Xoá bảng technician_jobs khi rollback migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('technician_jobs');
    }
};
