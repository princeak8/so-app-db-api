<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\PowerStationResource;
use App\Http\Resources\PowerUnitDataResource;

class PowerUnitEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "event" => $this->event,
            "load" => $this->load,
            "prevLoad" => $this->previous_load,
            "referenceLoad" => $this->reference_load,
            "frequency" => $this->frequency,
            "powerStation" => new PowerStationResource($this->whenLoaded('powerStation')),
            "powerUnit" => new PowerUnitResource($this->whenLoaded("powerUnit")),
        ];
    }
}
