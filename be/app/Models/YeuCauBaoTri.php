<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class YeuCauBaoTri extends Model
{
    use HasFactory;

    protected $table = 'yeu_cau_bao_tri';
    protected $primaryKey = 'id_yeu_cau';

    protected $fillable = [
        'id_cu_dan',
        'id_can_ho',
        'id_loai_su_co',
        'mo_ta',
        'thoi_gian_uu_tien',
        'trang_thai',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function cuDan()
    {
        return $this->belongsTo(NguoiDung::class, 'id_cu_dan', 'id_nguoi_dung');
    }

    public function canHo()
    {
        return $this->belongsTo(CanHo::class, 'id_can_ho', 'id_can_ho');
    }

    public function loaiSuCo()
    {
        return $this->belongsTo(LoaiSuCo::class, 'id_loai_su_co', 'id_loai_su_co');
    }

    public function hinhAnh()
    {
        return $this->hasMany(HinhAnhYeuCau::class, 'id_yeu_cau', 'id_yeu_cau');
    }

    public function phanCong()
    {
        return $this->hasMany(PhanCong::class, 'id_yeu_cau', 'id_yeu_cau');
    }

    public function phanHoi()
    {
        return $this->hasOne(PhanHoi::class, 'id_yeu_cau', 'id_yeu_cau');
    }

    public function nhatKyCongViec()
    {
        return $this->hasMany(NhatKyCongViec::class, 'id_yeu_cau', 'id_yeu_cau');
    }

    // Scopes
    public function scopeCuDanId($query, $id)
    {
        return $query->where('id_cu_dan', $id);
    }

    public function scopeTrangThai($query, $status)
    {
        return $query->where('trang_thai', $status);
    }
}
