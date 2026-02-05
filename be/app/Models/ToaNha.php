<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ToaNha extends Model
{
    use HasFactory;

    protected $table = 'toa_nha';
    protected $primaryKey = 'id_toa_nha';

    protected $fillable = [
        'ten_toa',
        'dia_chi',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function canHo()
    {
        return $this->hasMany(CanHo::class, 'id_toa_nha', 'id_toa_nha');
    }
}
