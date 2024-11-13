<?php

namespace App\Services;

use App\Models\LoadDrop;

class LoadDropService
{
    public $loadDropObj;

    public function loadDrop($id)
    {
        return LoadDrop::find($id);
    }

    public function stationLoadDrops($stationId)
    {
        return LoadDrop::where('power_station_id', $stationId)->get();
    }

    public function unacknowledgedStationLoadDrops($stationId)
    {
        return LoadDrop::where('power_station_id', $stationId)->whereNull('acknowledged_at')->get();
    }

    public function loadDropsByStationAndTimeOfDrop($stationId, $timeOfDrop)
    {
        return LoadDrop::where('power_station_id', $stationId)->where('time_of_drop', $timeOfDrop)->get();
    }

    public function latestLoadDrops($limit=10)
    {
        $limit = env('LATEST_LOAD_DROPS_LIMIT', $limit);
        return LoadDrop::distinct()
                        ->orderBy('time_of_drop', 'desc')->orderBy('created_at', 'desc')->limit($limit)->get();
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
            return LoadDrop::select('id', 'power_station_id', 'load', 'reference_load', 'previous_load', 'time_of_drop', 'acknowledged_at', 'calculation_type')
                            ->distinct()
                            ->where('time_of_drop', '>=', $start)->where('time_of_drop', '<=', $end)
                            ->groupBy('id', 'power_station_id', 'load', 'reference_load', 'previous_load', 'time_of_drop', 'acknowledged_at', 'calculation_type')
                            ->orderBy('time_of_drop', 'desc')
                            ->get();
        }else{
            return LoadDrop::select('id', 'power_station_id', 'load', 'reference_load', 'previous_load', 'time_of_drop', 'acknowledged_at', 'calculation_type')
                            ->distinct()
                            ->where('time_of_drop', '>=', $start)->where('time_of_drop', '<=', $end)
                            ->groupBy('id', 'power_station_id', 'load', 'reference_load', 'previous_load', 'time_of_drop', 'acknowledged_at', 'calculation_type')
                            ->orderBy('time_of_drop', 'desc')
                            ->get();
        }
    }


    public function save($data)
    {
        return LoadDrop::firstOrCreate([
            "power_station_id" => $data['powerStationId'],
            "load" => $data['load'],
            "previous_load" => $data['previousLoad'],
            "reference_load" => $data['referenceLoad'],
            "time_of_drop" => $data['timeOfDrop'],
            "calculation_type" => $data['calType']
        ]);
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

    public function acknowledge($data)
    {
        $this->loadDropObj->acknowledged_at = $data['acknowledgedAt'];
        $this->loadDropObj->save();
        return $this->loadDropObj;
    }

    public function acknowledgeStation($data)
    {
        if(isset($data['loadDrops']) && count($data['loadDrops']) > 0) {
            foreach($data['loadDrops'] as $loadDrop) {
                $loadDrop->acknowledged_at = $data['acknowledgedAt'];
                $loadDrop->update();
            }
        }
    }
}

?>