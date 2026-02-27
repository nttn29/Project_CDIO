<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TechnicianJob extends Model
{
    use HasFactory;

    protected $table = 'technician_jobs';

    protected $fillable = [
        'code',
        'title',
        'location',
        'description',
        'status',
        'priority',
        'scheduled_at',
        'due_at',
        'technician_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'due_at' => 'datetime',
    ];

    public const STATUS_MOI = 'moi';
    public const STATUS_DANG_XU_LY = 'dang_xu_ly';
    public const STATUS_HOAN_THANH = 'hoan_thanh';
    public const STATUS_HUY = 'huy';

    public const PRIORITY_THAP = 'thap';
    public const PRIORITY_TRUNG_BINH = 'trung_binh';
    public const PRIORITY_CAO = 'cao';

    public function technician()
    {
        return $this->belongsTo(NguoiDung::class, 'technician_id', 'id_nguoi_dung');
    }
}
