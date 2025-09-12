<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

use App\Models\PowerData;

class PowerDataService
{
    public $powerDataObj;

    public function powerData($id)
    {
        return PowerData::find($id);
    }

    public function stationData($stationId)
    {
        return PowerData::where('power_station_id', $stationId)->get();
    }

    public function powerDataByStationAndCapturedAt($stationId, $capturedAt)
    {
        return LoadDrop::where('power_station_id', $stationId)->where('captured_at', $capturedAt)->get();
    }

    public function latestPowerData($limit=10)
    {
        $limit = env('LATEST_LOAD_DROPS_LIMIT', $limit);
        return PowerData::select(DB::raw('DISTINCT ON (captured_at) *'))
                ->orderBy('captured_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
    }

    public function range($data, $group=false)
    {
        if(!isset($data['end'])) {
            $data['end'] = Date('Y-m-d H:i:s');
        }else{
            $endParts = explode(' ', $data['end']);
            $data['end'] = (isset($endParts[1])) ? $data['end'] : $data['end'].' 23:59:59';
        }
        $startParts = explode(' ', $data['start']);
        $start = (isset($startParts[1])) ? $data['start'] : $data['start'].' 00:00:00';
        $end = $data['end'];
        if($group) {
            return PowerData::select(DB::raw('DISTINCT ON (captured_at) *'))
                            ->where(function($query) {
                                $query->whereNot(function($q) {
                                    $q->where('load', 0)
                                    ->where('previous_load', 0);
                                    });
                                })
                            ->where('captured_at', '>=', $start)->where('captured_at', '<=', $end)
                            // ->groupBy('power_station_id')
                            ->orderBy('captured_at', 'desc')
                            ->get();
        }else{
            return PowerData::select(DB::raw('DISTINCT ON (captured_at) *'))
                            ->where('captured_at', '>=', $start)->where('captured_at', '<=', $end)
                            // ->groupBy('captured_at')
                            ->orderBy('captured_at', 'desc')
                            ->get();
        }
    }


    public function save($data)
    {
        $powerData = [
            "power_station_id" => $data['powerStationId'],
            "load" => $data['load'],
            "captured_at" => $data['capturedAt']
        ];
        if(isset($data['units'])) $powerData['units'] = $data['units'];
        return PowerData::firstOrCreate($powerData);
        // $loadDrop = new LoadDrop;
        // $loadDrop->power_station_id = $data['powerStationId'];
        // $loadDrop->load = $data['load'];
        // $loadDrop->previous_load = $data['previousLoad'];
        // $loadDrop->reference_load = $data['referenceLoad'];
        // $loadDrop->time_of_drop = $data['timeOfDrop'];
        // $loadDrop->calculation_type = $data['calType'];
        // $loadDrop->save();
        // return $loadDrop;
    }
}

?>