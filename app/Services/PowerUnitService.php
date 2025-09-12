<?php

namespace App\Services;

use App\Models\PowerUnit;

class PowerUnitService
{
    public function powerUnit($id)
    {
        return PowerUnit::find($id);
    }

    public function getByIdentifier(String $identifier)
    {
        return PowerUnit::where('identifier', $identifier)->first();
    }
}

?>