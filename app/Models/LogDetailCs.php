<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogDetailCs extends Model
{
    use HasFactory;

    protected $table = 'log_detail_cs';
    protected $primaryKey = 'id_det';
    
    protected $fillable = [
        'id_log',
        'list',
        'station',
        'check_item',
        'standard',
        'scanResult',
        'resultImage',
        'prod_status',
        'prod_checked_by',
        'prod_checked_at',
        'quality_status',
        'quality_checked_by',
        'quality_checked_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'prod_checked_at' => 'datetime',
        'quality_checked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function log()
    {
        return $this->belongsTo(LogCs::class, 'id_log', 'id_log');
    }
}
