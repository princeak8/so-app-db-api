<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\PowerStationResource;

use App\Helpers;

class LoadDropResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => (int)$this->id,
            "station" => new PowerStationResource($this->station),
            "load" => ($this->load != null) ? $this->load : 0,
            "referenceLoad" => ($this->reference_load != null) ? $this->reference_load : 0,
            "previousLoad" => ($this->previous_load != null) ? $this->previous_load : 0,
            "timeOfDrop" => $this->time_of_drop,
            "acknowledged_at" => $this->acknowledged_at,
            "calculationType" => $this->calculation_type,
            "prevLoadPercentage" => $this->getPercentage($this->load, $this->previous_load),
            "refLoadPercentage" => $this->getPercentage($this->load, $this->reference_load)
        ];
    }

    function getPercentage($target, $total) {
        $percentage = Helpers::percentageDiff($total, $target);
        return ($percentage['success']) ? $percentage['result'] : null;
    }
}
