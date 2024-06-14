<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\PowerStation;
use App\Enums\StationID;

class PowerStations extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $powerStations = [
            [ "name" => 'Afam IV', "identifier" => StationID::AfamIV ],
            [ "name" => 'Afam V', "identifier" => StationID::AfamV ],
            [ "name" => 'Afam VI', "identifier" => StationID::AfamVI ],
            [ "name" => 'Alaoji', "identifier" => StationID::Alaoji ],
            [ "name" => 'Azura-Edo IPP (Gas)', "identifier" => StationID::Azura ],
            [ "name" => 'Dadinkowa', "identifier" => StationID::Dadinkowa ],
            [ "name" => 'Delta 2 (Gas)', "identifier" => StationID::Delta2 ],
            [ "name" => 'Delta 3 (Gas)', "identifier" => StationID::Delta3 ],
            [ "name" => 'Delta 4 (Gas)', "identifier" => StationID::Delta4 ],
            [ "name" => 'Egbin', "identifier" => StationID::Egbin ],
            [ "name" => 'Geregu (Gas)', "identifier" => StationID::Geregu ],
            [ "name" => 'Geregu NIPP (Gas)', "identifier" => StationID::GereguNipp ],
            [ "name" => 'Gbarain (Gas)', "identifier" => StationID::Gbarain ],
            [ "name" => 'Ihovbor (Gas)', "identifier" => StationID::Ihovbor ],
            [ "name" => 'Ibom Power', "identifier" => StationID::Ibom ],
            [ "name" => 'Jebba (Hydro)', "identifier" => StationID::Jebba ],
            [ "name" => 'Kainji (Hydro)', "identifier" => StationID::Kainji ],
            [ "name" => 'Odukpani (Gas)', "identifier" => StationID::Odukpani ],
            [ "name" => 'Okpai (Gas/Steam)', "identifier" => StationID::Okpai ],
            [ "name" => 'Olorunsogo Gas', "identifier" => StationID::OlorunsogoGas ],
            [ "name" => 'Olorunsogo NIPP', "identifier" => StationID::OlorunsogoNipp ],
            [ "name" => 'Omoku (Gas)', "identifier" => StationID::Omoku ],
            [ "name" => 'Omotosho Gas', "identifier" => StationID::OmotoshoGas ],
            [ "name" => 'Omotosho NIPP', "identifier" => StationID::OmotoshoNipp],
            [ "name" => 'Paras Energy (Gas)', "identifier" => StationID::ParasEnergy ],
            [ "name" => 'Rivers IPP (Gas)', "identifier" => StationID::RiversIpp ],
            [ "name" => 'Sapele NIPP', "identifier" => StationID::SapeleNipp ],
            [ "name" => 'Sapele (Steam)', "identifier" => StationID::SapeleSteam ],
            [ "name" => 'Shiroro (Hydro)', "identifier" => StationID::Shiroro ],
            [ "name" => 'Taopex', "identifier" => StationID::Taopex ],
            [ "name" => 'Transamadi (Gas)', "identifier" => StationID::Transamadi ],
            [ "name" => 'Zungeru', "identifier" => StationID::Zungeru ],
        ];

        foreach($powerStations as $station) {
            $powerStation = new PowerStation;
            $powerStation->name = $station['name'];
            $powerStation->identifier = $station['identifier'];
            $powerStation->save();
        }
    }
}
