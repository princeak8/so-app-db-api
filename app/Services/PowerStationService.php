<?php

namespace App\Services;

use App\Models\PowerStation;

class PowerStationService
{
    public function powerStation($id)
    {
        return PowerStation::find($id);
    }

    public function powerStationByIdentifier(String $identifier)
    {
        return PowerStation::where('identifier', $identifier)->first();
    }
}

?>