<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YeuCauBt extends Model
{
    protected $table = 'yeu_cau_bt';

   protected $fillable = [
    'id_chu_can_ho',
    'noi_dung',
    'trang_thai',
    'id_ky_thuat_vien' // 👈 THÊM DÒNG NÀY
];

  
   public function kyThuatVien()
{
    return $this->belongsTo(\App\Models\KyThuatVien::class, 'id_ky_thuat_vien');
}

public function chuCanHo()
{
    return $this->belongsTo(\App\Models\ChuCanHo::class, 'id_chu_can_ho');
}
}