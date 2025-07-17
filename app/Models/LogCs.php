<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogCs extends Model
{
    use HasFactory;

    protected $table = 'log_cs';
    protected $primaryKey = 'id_log';
    
    protected $fillable = [
        'area',
        'line',
        'model',
        'shift',
        'date',
        'status',
        'image'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public $timestamps = false;

    public function details()
    {
        return $this->hasMany(LogDetailCs::class, 'id_log', 'id_log');
    }
}
