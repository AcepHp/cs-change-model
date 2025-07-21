<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartModel extends Model
{
    protected $table = 'part_model';

    protected $primaryKey = 'id'; 

    public $timestamps = false;

    protected $fillable = [
        'PartNoBaan',
        'Model'
    ];
}
