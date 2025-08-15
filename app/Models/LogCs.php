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
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public $timestamps = false;

    public function details()
    {
        return $this->hasMany(LogDetailCs::class, 'id_log', 'id_log');
    }
    
    public function partModelRelation()
    {
        return $this->belongsTo(PartModel::class, 'model', 'Model')
                    ->select(['id', 'Model', 'frontView']); // Pastikan kolom yang dibutuhkan dipilih
    }
    
    public function getFrontViewAttribute()
    {
        return $this->partModelRelation ? $this->partModelRelation->frontView : null;
    }
}
