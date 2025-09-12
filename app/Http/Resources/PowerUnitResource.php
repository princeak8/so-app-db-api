<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\PowerStationResource;
use App\Http\Resources\PowerDataResource;
use App\Http\Resources\PowerUnitEventResource;

class PowerUnitResource extends JsonResource
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
            "identifier" => $this->identifier,
            "name" => $this->name,
            "powerStation" => new PowerStationResource($this->whenLoaded('powerStation')),
            "data" => PowerUnitDataResource::collection($this->whenLoaded("data")),
            "events" => PowerUnitEventResource::collection($this->whenLoaded("events"))
        ];
    }
}
