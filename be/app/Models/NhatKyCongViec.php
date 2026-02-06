<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NhatKyCongViec extends Model
{
    use HasFactory;

    protected $table = 'nhat_ky_cong_viec';
    protected $primaryKey = 'id_nhat_ky';

    protected $fillable = [
        'id_yeu_cau',
        'id_phan_cong',
        'id_nhan_vien',
        'mo_ta_cong_viec',
        'ngay_thuc_hien',
        'ghi_chu',
    ];

    protected $casts = [
        'ngay_thuc_hien' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function yeuCau()
    {
        return $this->belongsTo(YeuCauBaoTri::class, 'id_yeu_cau', 'id_yeu_cau');
    }

    public function phanCong()
    {
        return $this->belongsTo(PhanCong::class, 'id_phan_cong', 'id_phan_cong');
    }

    public function nhanVien()
    {
        return $this->belongsTo(NguoiDung::class, 'id_nhan_vien', 'id_nguoi_dung');
    }
}
