<?php

// app/Models/CsChangeModel.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChangeModel extends Model
{
    protected $table = 'cs_changemodel';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'area', 'line', 'model', 'list', 'station', 'check_item', 'standard', 'actual', 'trigger',
    ];
}

