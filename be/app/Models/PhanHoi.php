<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhanHoi extends Model
{
    use HasFactory;

    protected $table = 'phan_hoi';
    protected $primaryKey = 'id_phan_hoi';

    protected $fillable = [
        'id_yeu_cau',
        'id_cu_dan',
        'danh_gia',
        'binh_luan',
    ];

    protected $casts = [
        'danh_gia' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function yeuCau()
    {
        return $this->belongsTo(YeuCauBaoTri::class, 'id_yeu_cau', 'id_yeu_cau');
    }

    public function cuDan()
    {
        return $this->belongsTo(NguoiDung::class, 'id_cu_dan', 'id_nguoi_dung');
    }

    // Scope to validate rating is between 1-5
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if ($model->danh_gia < 1 || $model->danh_gia > 5) {
                throw new \Exception('Rating must be between 1 and 5');
            }
        });
    }
}
