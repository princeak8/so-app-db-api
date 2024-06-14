<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadDrop extends Model
{
    use HasFactory;

    public function station()
    {
        return $this->belongsTo('App\Models\PowerStation', 'power_station_id', 'id');
    }
}
