<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PowerUnitEvent extends Model
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
}
