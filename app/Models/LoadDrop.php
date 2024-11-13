<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadDrop extends Model
{
    use HasFactory;

    protected $fillable = [
        "power_station_id",
        "load",
        "previous_load",
        "reference_load",
        "time_of_drop",
        "calculation_type"
    ];

    public function station()
    {
        return $this->belongsTo('App\Models\PowerStation', 'power_station_id', 'id');
    }
}
