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
            [ "name" => 'Afam IV', "identifier" => StationID::AfamIV->value ],
            [ "name" => 'Afam V', "identifier" => StationID::AfamV->value ],
            [ "name" => 'Afam VI', "identifier" => StationID::AfamVI->value ],
            [ "name" => 'Alaoji', "identifier" => StationID::Alaoji->value ],
            [ "name" => 'Azura-Edo IPP (Gas)', "identifier" => StationID::Azura->value ],
            [ "name" => 'Dadinkowa', "identifier" => StationID::Dadinkowa->value ],
            [ "name" => 'Delta 2 (Gas)', "identifier" => StationID::Delta2->value ],
            [ "name" => 'Delta 3 (Gas)', "identifier" => StationID::Delta3->value ],
            [ "name" => 'Delta 4 (Gas)', "identifier" => StationID::Delta4->value ],
            [ "name" => 'Egbin', "identifier" => StationID::Egbin->value ],
            [ "name" => 'Geregu (Gas)', "identifier" => StationID::Geregu->value ],
            [ "name" => 'Geregu NIPP (Gas)', "identifier" => StationID::GereguNipp->value ],
            [ "name" => 'Gbarain (Gas)', "identifier" => StationID::Gbarain->value ],
            [ "name" => 'Ihovbor (Gas)', "identifier" => StationID::Ihovbor->value ],
            [ "name" => 'Ibom Power', "identifier" => StationID::Ibom->value ],
            [ "name" => 'Jebba (Hydro)', "identifier" => StationID::Jebba->value ],
            [ "name" => 'Kainji (Hydro)', "identifier" => StationID::Kainji->value ],
            [ "name" => 'Odukpani (Gas)', "identifier" => StationID::Odukpani->value ],
            [ "name" => 'Okpai (Gas/Steam)', "identifier" => StationID::Okpai->value ],
            [ "name" => 'Olorunsogo Gas', "identifier" => StationID::OlorunsogoGas->value ],
            [ "name" => 'Olorunsogo NIPP', "identifier" => StationID::OlorunsogoNipp->value ],
            [ "name" => 'Omoku (Gas)', "identifier" => StationID::Omoku->value ],
            [ "name" => 'Omotosho Gas', "identifier" => StationID::OmotoshoGas->value ],
            [ "name" => 'Omotosho NIPP', "identifier" => StationID::OmotoshoNipp],
            [ "name" => 'Paras Energy (Gas)', "identifier" => StationID::ParasEnergy->value ],
            [ "name" => 'Rivers IPP (Gas)', "identifier" => StationID::RiversIpp->value ],
            [ "name" => 'Sapele NIPP', "identifier" => StationID::SapeleNipp->value ],
            [ "name" => 'Sapele (Steam)', "identifier" => StationID::SapeleSteam->value ],
            [ "name" => 'Shiroro (Hydro)', "identifier" => StationID::Shiroro->value ],
            [ "name" => 'Taopex', "identifier" => StationID::Taopex->value ],
            [ "name" => 'Transamadi (Gas)', "identifier" => StationID::Transamadi->value ],
            [ "name" => 'Zungeru', "identifier" => StationID::Zungeru->value ],
        ];

        foreach($powerStations as $station) {
            $powerStation = new PowerStation;
            $powerStation->name = $station['name'];
            $powerStation->identifier = $station['identifier'];
            $powerStation->save();
        }
    }
}
