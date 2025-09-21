<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PowerUnitData extends Model
{
    use HasFactory;

    protected $fillable = [
        "power_station_id",
        "power_unit_id",
        "power_data_id",
        "mw",
        "kv",
        "a",
        "mx",
        "frequency",
        "captured_at"
    ];

    public function powerStation()
    {
        return $this->belongsTo(PowerStation::class);
    }

    public function powerUnit()
    {
        return $this->belongsTo(PowerUnit::class);
    }

    public function powerData()
    {
        return $this->belongsTo(PowerData::class);
    }
}
