<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HinhAnhYeuCau extends Model
{
    use HasFactory;

    protected $table = 'hinh_anh_yeu_cau';
    protected $primaryKey = 'id_hinh_anh';

    protected $fillable = [
        'id_yeu_cau',
        'duong_dan_file',
        'ten_file',
        'mime_type',
        'kich_thuoc',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'kich_thuoc' => 'integer',
    ];

    // Relationships
    public function yeuCau()
    {
        return $this->belongsTo(YeuCauBaoTri::class, 'id_yeu_cau', 'id_yeu_cau');
    }
}
