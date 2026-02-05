<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoaiSuCo extends Model
{
    use HasFactory;

    protected $table = 'loai_su_co';
    protected $primaryKey = 'id_loai_su_co';

    protected $fillable = [
        'ten_loai',
        'muc_uu_tien',
        'mo_ta',
    ];

    protected $casts = [
        'muc_uu_tien' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function yeuCauBaoTri()
    {
        return $this->hasMany(YeuCauBaoTri::class, 'id_loai_su_co', 'id_loai_su_co');
    }
}
