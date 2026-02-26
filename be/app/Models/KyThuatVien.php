<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KyThuatVien extends Model
{
    protected $table = 'ky_thuat_vien';

    protected $fillable = [
        'ten',
        'so_dien_thoai',
        'trang_thai'
    ];
 public function yeuCauBt()
{
    return $this->hasMany(\App\Models\YeuCauBt::class, 'id_ky_thuat_vien');
}
}


 