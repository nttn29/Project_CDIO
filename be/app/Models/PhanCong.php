<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhanCong extends Model
{
    use HasFactory;

    protected $table = 'phan_cong';
    protected $primaryKey = 'id_phan_cong';

    protected $fillable = [
        'id_yeu_cau',
        'id_nhan_vien',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'trang_thai',
    ];

    protected $casts = [
        'ngay_bat_dau' => 'datetime',
        'ngay_ket_thuc' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function yeuCau()
    {
        return $this->belongsTo(YeuCauBaoTri::class, 'id_yeu_cau', 'id_yeu_cau');
    }

    public function nhanVien()
    {
        return $this->belongsTo(NguoiDung::class, 'id_nhan_vien', 'id_nguoi_dung');
    }

    public function nhatKyCongViec()
    {
        return $this->hasMany(NhatKyCongViec::class, 'id_phan_cong', 'id_phan_cong');
    }
}
