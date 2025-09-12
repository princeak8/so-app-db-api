<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PowerUnitData extends Model
{
    use HasFactory;

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
