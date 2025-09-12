<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\PowerStationResource;
use App\Http\Resources\PowerUnitDataResource;

class PowerDataResource extends JsonResource
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
            "load" => $this->load,
            "frequency" => $this->frequency,
            "capturedAt" => $this->captured_at,
            "powerStation" => new PowerStationResource($this->whenLoaded('powerStation')),
            "unitsData" => PowerUnitDataResource::collection($this->whenLoaded("unitsData"))
        ];

        /* 
            $table->decimal('load', 5, 2);
            $table->decimal('frequency', 4, 2);
            $table->dateTime("captured_at");
        */
    }
}
