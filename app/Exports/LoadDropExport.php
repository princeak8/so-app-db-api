<?php

namespace App\Exports;

use App\Models\LoadDrop;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Illuminate\Database\Eloquent\Collection;

class LoadDropExport implements FromCollection
// class LoadDropExport implements FromArray
{
    protected $loadDrops;

    public function __construct(Collection $loadDrops)
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
}
