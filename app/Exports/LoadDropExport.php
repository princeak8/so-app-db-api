<?php

namespace App\Exports;

use App\Models\LoadDrop;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use PhpParser\Node\Expr\Cast\Array_;

class LoadDropExport implements FromCollection, WithHeadings
// class LoadDropExport implements FromArray
{
    protected $loadDrops;

    public function __construct(AnonymousResourceCollection $loadDrops)
    {
        $this->loadDrops = $loadDrops;
    }

    // /**
    // * @return \Illuminate\Support\Collection
    // */
    public function collection()
    {
        // return LoadDrop::all();
        return $this->loadDrops;
    }

    // public function array(): array
    // {
    //     return $this->loadDrops;
    // }

    public function headings(): array
    {
        return ["STATION", "LOAD", "LOAD DROP", "PREVIOUS LOAD", "REFERENCE LOAD", "PREVIOUS LOAD %", "REFERENCE LOAD %", "TIME OF DROP"];
    }
}