<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CanHo extends Model
{
    use HasFactory;

    protected $table = 'can_ho';
    protected $primaryKey = 'id_can_ho';

    protected $fillable = [
        'id_toa_nha',
        'so_can_ho',
        'tang',
        'id_cu_dan',
    ];

    protected $casts = [
        'tang' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function toaNha()
    {
        return $this->belongsTo(ToaNha::class, 'id_toa_nha', 'id_toa_nha');
    }

    public function cuDan()
    {
        return $this->belongsTo(NguoiDung::class, 'id_cu_dan', 'id_nguoi_dung');
    }

    public function yeuCauBaoTri()
    {
        return $this->hasMany(YeuCauBaoTri::class, 'id_can_ho', 'id_can_ho');
    }
}
