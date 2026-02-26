<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChuCanHo extends Model
{
    protected $table = 'chu_can_ho';

    protected $fillable = [
        'ho_ten',
        'cccd',
        'so_dien_thoai',
        'dia_chi_thuong_tru',
        'so_nha',
        'ngay_dang_ky'
    ];

}