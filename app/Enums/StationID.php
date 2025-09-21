<?php

namespace App\Enums;

enum StationID: string
    {
        case AfamIII = 'afamIIIPs';
        case AfamIV = 'afamIv_vPs';
        case AfamV = 'afamVPs';
        case AfamVI = 'afamVIPs';
        case Alaoji = 'alaoji';
        case Azura = 'azuraIppPs';
        case Dadinkowa = 'dadinKowaGs';
        case Delta2 = 'delta2';
        case Delta3 = 'delta3';
        case Delta4 = 'deltaGs';
        case Egbin = 'egbinPs';
        case Geregu = 'gereguPs';
        case GereguNipp = 'gereguNipp';
        case Gbarain = 'gbarain';
        case Ihovbor = 'ihovborNippPs';
        case Jebba = 'jebbaTs';
        case Kainji = 'kainjiTs';
        case Odukpani = 'odukpaniNippPs';
        case Okpai = 'okpaiGs';
        case Omoku = 'omokuPs1';
        case Omotosho1 = 'omotosho1';
        case Omotosho2 = 'omotosho2';
        case OmotoshoNipp = 'omotoshoNippPs';
        case ParasEnergy = 'parasEnergyPs';
        case RiversIpp = 'riversIppPs';
        case SapeleNipp = 'sapeleNipp';
        case Sapele = 'sapele';
        case Shiroro = 'shiroroPs';
        case Taopex = 'taopex';
        case Transamadi = 'phMain';
        case Zungeru = 'zungeru';

        case Eket = 'eket';
        case Ekim = 'ekim';
        case Olorunsogo1 = 'olorunsogo1';
        case Olorunsogo2 = 'olorunsogo2';
        case OlorunsogoLines = 'olorunsogoLines';

        case OmotoshoGas = 'omotoshoGas';
        case OlorunsogoGas = 'olorunsogoGas';
        case OlorunsogoNipp = 'olorunsogoNipp';
        case Ibom = "ibom";
    }