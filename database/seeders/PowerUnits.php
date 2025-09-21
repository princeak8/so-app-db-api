<?php

namespace Database\Seeders;

use App\Models\PowerUnit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Services\PowerStationService;
use App\Services\PowerUnitService;

class PowerUnits extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $powerStations = [
            "afamIIIPs" => [
                ["name" => "TM23", "identifier" => "tm23"],
                ["name" => "TM24", "identifier" => "tm24"],
                ["name" => "TM25", "identifier" => "tm25"],
            ],
            "afamIv_vPs" => [
                ["name" => "GT17", "identifier" => "gt17"],
                ["name" => "GT18", "identifier" => "gt18"],
            ],
            "afamVPs" => [
                ["name" => "GT19", "identifier" => "gt19"],
                ["name" => "GT20", "identifier" => "gt20"]
            ],
            "afamVIPs" => [
                ["name" => "GT11", "identifier" => "gt11"],
                ["name" => "GT12", "identifier" => "gt12"],
                ["name" => "GT13", "identifier" => "gt13"],
                ["name" => "ST1", "identifier" => "st1"]
            ],
            "delta2" => [
                ["name" => "GT4", "identifier" => "gt4"],
                ["name" => "GT5", "identifier" => "gt5"],
                ["name" => "GT6", "identifier" => "gt6"],
                ["name" => "GT7", "identifier" => "gt7"],
                ["name" => "GT8", "identifier" => "gt8"]
            ],
            "delta3" => [
                ["name" => "GT9", "identifier" => "gt9"],
                ["name" => "GT10", "identifier" => "gt10"],
                ["name" => "GT11", "identifier" => "gt11"],
                ["name" => "GT12", "identifier" => "gt12"],
                ["name" => "GT13", "identifier" => "gt13"]
            ],
            "deltaGs" => [
                ["name" => "GT15", "identifier" => "gt15"],
                ["name" => "GT16", "identifier" => "gt16"],
                ["name" => "GT17", "identifier" => "gt17"],
                ["name" => "GT18", "identifier" => "gt18"],
                ["name" => "GT19", "identifier" => "gt19"],
                ["name" => "GT20", "identifier" => "gt20"]
            ],
            "egbinPs" => [
                ["name" => "ST1", "identifier" => "st1"],
                ["name" => "ST2", "identifier" => "st2"],
                ["name" => "ST3", "identifier" => "st3"],
                ["name" => "ST4", "identifier" => "st4"],
                ["name" => "ST5", "identifier" => "st5"],
                ["name" => "ST6", "identifier" => "st6"]
            ],
            "sapele" => [
                ["name" => "PB202", "identifier" => "pb202"],
                ["name" => "PB203", "identifier" => "pb203"],
                ["name" => "PB204", "identifier" => "pb204"],
                ["name" => "PB210", "identifier" => "pb210"],
                ["name" => "ST1", "identifier" => "st1"],
                ["name" => "ST2", "identifier" => "st2"],
                ["name" => "ST3", "identifier" => "st3"],
                ["name" => "ST4", "identifier" => "st4"],
            ],
            "sapeleNipp" => [
                ["name" => "GT1", "identifier" => "gt1"],
                ["name" => "GT2", "identifier" => "gt2"],
                ["name" => "GT3", "identifier" => "gt3"],
                ["name" => "GT4", "identifier" => "gt4"]
            ],
        ];

        $powerStationService = new PowerStationService;
        $powerUnitService = new PowerUnitService;
        foreach($powerStations as $identifier=>$units) {
            $powerStation = $powerStationService->powerStationByIdentifier($identifier);
            if($powerStation) {
                foreach($units as $unit) {
                    $powerUnit = $powerUnitService->getByIdentifier($unit["identifier"]);
                    if(!$powerUnit) {
                        $powerUnit = new PowerUnit;
                        // dd($powerStation->id);
                        $powerUnit->power_station_id = $powerStation->id;
                        $powerUnit->name = $unit["name"];
                        $powerUnit->identifier = $unit["identifier"];
                        $powerUnit->save();
                    }
                    // dd($powerUnit);
                }
            }
        }
    }
}
