<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PowerStation extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function units()
    {
        return $this->hasMany(PowerUnit::class);
    }

    public function data()
    {
        return $this->hasMany(PowerData::class);
    }

    public function unitEvents()
    {
        return $this->hasMany(PowerStationEvent::class, "power_station_id", "id");
    }
}
