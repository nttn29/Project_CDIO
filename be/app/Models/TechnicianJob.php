<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model đại diện cho bảng technician_jobs.
 *
 * Mỗi bản ghi tương ứng một công việc kỹ thuật viên cần thực hiện.
 * Bảng này được đồng bộ tự động từ bảng phan_cong mỗi khi admin
 * tạo phân công mới (xem PhanCongController::store).
 *
 * Các cột chính:
 *  - code        : mã định danh dạng "PC-{id_phan_cong}" hoặc tùy chỉnh
 *  - title       : tên/tiêu đề công việc
 *  - location    : vị trí thực hiện (ví dụ: "Can ho 101")
 *  - description : mô tả chi tiết yêu cầu
 *  - status      : trạng thái công việc (moi | dang_xu_ly | hoan_thanh | huy)
 *  - priority    : mức độ ưu tiên (thap | trung_binh | cao)
 *  - scheduled_at: thời điểm dự kiến thực hiện
 *  - due_at      : hạn chót hoàn thành
 *  - technician_id: khóa ngoại trỏ tới bảng nguoi_dung (id_nguoi_dung)
 */
class TechnicianJob extends Model
{
    use HasFactory;

    /** Tên bảng trong CSDL */
    protected $table = 'technician_jobs';

    /** Các cột cho phép gán hàng loạt (mass-assignment) */
    protected $fillable = [
        'code',
        'title',
        'location',
        'description',
        'status',
        'priority',
        'scheduled_at',
        'due_at',
        'technician_id',
    ];

    /** Tự động cast các cột ngày giờ sang đối tượng Carbon */
    protected $casts = [
        'scheduled_at' => 'datetime',
        'due_at' => 'datetime',
    ];

    // ── Hằng số trạng thái công việc ──────────────────────────────────────
    public const STATUS_MOI         = 'moi';          // Công việc mới, chưa xử lý
    public const STATUS_DANG_XU_LY  = 'dang_xu_ly';  // Kỹ thuật viên đang thực hiện
    public const STATUS_HOAN_THANH  = 'hoan_thanh';  // Đã hoàn thành
    public const STATUS_HUY         = 'huy';          // Đã huỷ

    // ── Hằng số mức độ ưu tiên ────────────────────────────────────────────
    public const PRIORITY_THAP      = 'thap';         // Ưu tiên thấp
    public const PRIORITY_TRUNG_BINH = 'trung_binh'; // Ưu tiên trung bình
    public const PRIORITY_CAO       = 'cao';          // Ưu tiên cao (khẩn cấp)

    /**
     * Quan hệ: công việc này thuộc về một kỹ thuật viên (NguoiDung).
     * Khóa ngoại: technician_id → nguoi_dung.id_nguoi_dung
     */
    public function technician()
    {
        return $this->belongsTo(NguoiDung::class, 'technician_id', 'id_nguoi_dung');
    }
}
