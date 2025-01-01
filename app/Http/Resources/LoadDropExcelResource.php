<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\PowerStationResource;

use App\Helpers;

class LoadDropExcelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "station" => $this->station->name,
            "load" => ($this->load != null) ? $this->load : 0,
            "loadDrop" => $this->previous_load - $this->load,
            "previousLoad" => ($this->previous_load != null) ? $this->previous_load : 0,
            "referenceLoad" => ($this->reference_load != null) ? $this->reference_load : 0,
            "prevLoadPercentage" => number_format($this->getPercentage($this->load, $this->previous_load), 2),
            "refLoadPercentage" => number_format($this->getPercentage($this->load, $this->reference_load), 2),
            "timeOfDrop" => $this->time_of_drop
        ];
    }

    function getPercentage($target, $total) {
        $percentage = Helpers::percentageDiff($total, $target);
        return ($percentage['success']) ? $percentage['result'] : null;
    }
}
