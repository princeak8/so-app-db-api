<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\PowerStationResource;
use App\Http\Resources\PowerUnitResource;
use App\Http\Resources\PowerDataResource;

class PowerUnitDataResource extends JsonResource
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
            "mw" => $this->mw,
            "kv" => $this->kv,
            "a" => $this->a,
            "mx" => $this->mx,
            "frequency" => $this->frequency,
            "capturedAt" => $this->captured_at,
            "powerStation" => new PowerStationResource($this->whenLoaded('powerStation')),
            "powerUnit" => new PowerUnitResource($this->whenLoaded("powerUnit")),
            "powerData" => new PowerDataResource($this->whenLoaded("powerData"))
        ];
    }
}
