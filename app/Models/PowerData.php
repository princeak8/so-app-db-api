<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PowerData extends Model
{
    use HasFactory;

    protected $fillable = [
        "power_station_id",
        "load",
        "frequency",
        "captured_at"
    ];

    public function PowerStation()
    {
        return $this->belongsTo(PowerStation::class);
    }

    public function unitsData()
    {
        return $this->hasMany(PowerUnitData::class, "power_data_id", "id");
    }
}
