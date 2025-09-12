<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PowerUnit extends Model
{
    use HasFactory;

    public function powerStation()
    {
        return $this->belongsTo(PowerStation::class);
    }

    public function data()
    {
        return $this->hasMany(PowerUnitData::class, "power_unit_id", "id");
    }

    public function events()
    {
        return $this->hasMany(PowerUnitEvent::class, "power_unit_id", "id");
    }
}
