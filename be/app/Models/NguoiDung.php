<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;

class NguoiDung extends Model
{
    use HasFactory;

    protected $table = 'nguoi_dung';
    protected $primaryKey = 'id_nguoi_dung';
    
    protected $fillable = [
        'email',
        'ten',
        'mat_khau',
        'dien_thoai',
        'vai_tro',
        'trang_thai',
    ];

    protected $hidden = [
        'mat_khau',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Mutator: hash password when setting
    public function setMatKhauAttribute($value)
    {
        $this->attributes['mat_khau'] = Hash::make($value);
    }

    // Relationships
    public function canHo()
    {
        return $this->hasOne(CanHo::class, 'id_cu_dan', 'id_nguoi_dung');
    }

    public function yeuCauBaoTri()
    {
        return $this->hasMany(YeuCauBaoTri::class, 'id_cu_dan', 'id_nguoi_dung');
    }

    public function phanHoi()
    {
        return $this->hasMany(PhanHoi::class, 'id_cu_dan', 'id_nguoi_dung');
    }

    public function phanCong()
    {
        return $this->hasMany(PhanCong::class, 'id_nhan_vien', 'id_nguoi_dung');
    }

    public function nhatKyCongViec()
    {
        return $this->hasMany(NhatKyCongViec::class, 'id_nhan_vien', 'id_nguoi_dung');
    }
}
