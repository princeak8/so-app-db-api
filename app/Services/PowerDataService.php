<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

use App\Models\PowerData;
use App\Models\PowerUnitData;
use App\Utilities;

class PowerDataService
{
    public $powerDataObj;

    public function powerData($id)
    {
        return PowerData::find($id);
    }

    /**
     * Get all data for a specific station (with time-series optimization)
     */
    public function stationData(int $stationId, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $query = PowerData::where('power_station_id', $stationId);
        
        if ($from && $to) {
            $query->whereBetween('captured_at', [$from, $to]);
        }
        
        return $query->orderBy('captured_at', 'desc')->get();
    }

    // public function stationData($stationId)
    // {
    //     return PowerData::where('power_station_id', $stationId)->get();
    // }

    /**
     * Get power data by station and specific captured time
     */
    public function powerDataByStationAndCapturedAt(int $stationId, string $capturedAt): Collection
    {
        return PowerData::where('power_station_id', $stationId)
                       ->where('captured_at', $capturedAt)
                       ->get();
    }

    /**
     * Get latest power data with TimescaleDB optimization
     */
    // public function latestPowerData(int $limit = 10): Collection
    // {
    //     $limit = env('LATEST_POWER_DATA_LIMIT', $limit);
        
    //     // Use TimescaleDB's last() function for better performance
    //     return collect(DB::select("
    //         SELECT DISTINCT ON (power_station_id) 
    //                id, power_station_id, load, frequency, captured_at, created_at, updated_at
    //         FROM power_data 
    //         ORDER BY power_station_id, captured_at DESC 
    //         LIMIT ?
    //     ", [$limit]))->map(function ($item) {
    //         return (object) $item;
    //     });
    // }

    public function latestPowerData($limit=10)
    {
        $limit = env('LATEST_LOAD_DROPS_LIMIT', $limit);
        return PowerData::select(DB::raw('DISTINCT ON (captured_at) *'))
                ->orderBy('captured_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
    }

    // public function range($data, $group=false)
    // {
    //     if(!isset($data['end'])) {
    //         $data['end'] = Date('Y-m-d H:i:s');
    //     }else{
    //         $endParts = explode(' ', $data['end']);
    //         $data['end'] = (isset($endParts[1])) ? $data['end'] : $data['end'].' 23:59:59';
    //     }
    //     $startParts = explode(' ', $data['start']);
    //     $start = (isset($startParts[1])) ? $data['start'] : $data['start'].' 00:00:00';
    //     $end = $data['end'];
    //     if($group) {
    //         return PowerData::select(DB::raw('DISTINCT ON (captured_at) *'))
    //                         ->where(function($query) {
    //                             $query->whereNot(function($q) {
    //                                 $q->where('load', 0)
    //                                 ->where('previous_load', 0);
    //                                 });
    //                             })
    //                         ->where('captured_at', '>=', $start)->where('captured_at', '<=', $end)
    //                         // ->groupBy('power_station_id')
    //                         ->orderBy('captured_at', 'desc')
    //                         ->get();
    //     }else{
    //         return PowerData::select(DB::raw('DISTINCT ON (captured_at) *'))
    //                         ->where('captured_at', '>=', $start)->where('captured_at', '<=', $end)
    //                         // ->groupBy('captured_at')
    //                         ->orderBy('captured_at', 'desc')
    //                         ->get();
    //     }
    // }

    /**
     * Get power data within time range with TimescaleDB optimizations
     */
    public function range(array $data, bool $group = false): Collection
    {
        // Handle end date
        if (!isset($data['end'])) {
            $data['end'] = now()->toDateTimeString();
        } else {
            $endParts = explode(' ', $data['end']);
            $data['end'] = isset($endParts[1]) ? $data['end'] : $data['end'] . ' 23:59:59';
        }

        // Handle start date
        $startParts = explode(' ', $data['start']);
        $start = isset($startParts[1]) ? $data['start'] : $data['start'] . ' 00:00:00';
        $end = $data['end'];

        if ($group) {
            // Use TimescaleDB time_bucket for efficient grouping
            return collect(DB::select("
                SELECT 
                    time_bucket('1 hour', captured_at) AS time_bucket,
                    power_station_id,
                    AVG(load) as avg_load,
                    MAX(load) as max_load,
                    MIN(load) as min_load,
                    AVG(frequency) as avg_frequency,
                    COUNT(*) as readings_count
                FROM power_data 
                WHERE captured_at BETWEEN ? AND ?
                  AND NOT (load = 0 AND frequency = 0)
                GROUP BY time_bucket, power_station_id 
                ORDER BY time_bucket DESC, power_station_id
            ", [$start, $end]));
        } else {
            // Optimized query without DISTINCT ON for better performance
            return PowerData::whereBetween('captured_at', [$start, $end])
                          ->orderBy('captured_at', 'desc')
                          ->get();
        }
    }

    /**
     * Time-series aggregated data using TimescaleDB functions
     */
    public function getAggregatedData(
        int $stationId, 
        Carbon $from, 
        Carbon $to, 
        string $interval = '1 hour'
    ): Collection {
        return collect(DB::select("
            SELECT 
                time_bucket(?, captured_at) AS time_bucket,
                AVG(load) as avg_load,
                MAX(load) as max_load,
                MIN(load) as min_load,
                STDDEV(load) as load_stddev,
                AVG(frequency) as avg_frequency,
                MAX(frequency) as max_frequency,
                MIN(frequency) as min_frequency,
                COUNT(*) as readings_count
            FROM power_data 
            WHERE power_station_id = ? 
            AND captured_at BETWEEN ? AND ?
            GROUP BY time_bucket 
            ORDER BY time_bucket DESC
        ", [$interval, $stationId, $from, $to]));
    }

    /**
     * Get real-time streaming data (last N minutes)
     */
    public function getRecentData(int $stationId, int $minutes = 10): Collection
    {
        $since = Carbon::now()->subMinutes($minutes);
        
        return PowerData::where('power_station_id', $stationId)
                       ->where('captured_at', '>=', $since)
                       ->orderBy('captured_at', 'desc')
                       ->get();
    }

    /**
     * Efficient bulk save using TimescaleDB optimized inserts
     */
    public function bulkSave(array $dataArray): bool
    {
        $insertData = [];
        
        foreach ($dataArray as $key=>$data) {
            // dd($data);
            if(!isset($data['powerStationId'])) {
                Utilities::logStuff($key);
                Utilities::logStuff($data);
                Utilities::logStuff($dataArray);
            }
            $powerData["data"] = [
                "power_station_id" => $data['powerStationId'],
                "load" => $data['load'],
                "frequency" => $data['frequency'],
                "captured_at" => $data['capturedAt'],
                "created_at" => now(),
                "updated_at" => now()
            ];
            if(isset($data['unitsData'])) $powerData['unitsData'] = $data['unitsData'];
            $insertData[] = $powerData;
        }

        // Use chunking for large datasets
        $chunks = array_chunk($insertData, 1000);
        
        DB::transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                foreach($chunk as $chunkData) {
                    // dd($chunkData['data']);
                    $powerData = PowerData::firstOrCreate($chunkData["data"]);
                    if(isset($chunkData['unitsData'])) {
                        $unitsData = $chunkData['unitsData'];
                        foreach($unitsData as $unitData) {
                            // dd($unitData);
                            PowerUnitData::firstOrCreate([
                                "power_station_id" => $unitData["powerStationId"],
                                "power_unit_id" => $unitData["powerUnitId"],
                                "power_data_id" => $powerData->id,
                                "mw" => $unitData["mw"],
                                "kv" => $unitData["kv"],
                                "a" => $unitData["a"],
                                "mx" => $unitData["mx"],
                                "frequency" => $unitData["frequency"],
                                "captured_at" => $unitData["capturedAt"],
                            ]);
                        }
                    }
                }
            }
        });

        return true;
    }

    /**
     * Single record save (optimized)
     */
    public function save(array $data): PowerData
    {
        $powerData = [
            "power_station_id" => $data['powerStationId'],
            "load" => $data['load'],
            "frequency" => $data['frequency'],
            "captured_at" => $data['capturedAt']
        ];

        // For time-series data, usually we want to insert rather than update
        // Use create instead of firstOrCreate for better performance
        return PowerData::create($powerData);
    }

    /**
     * Get continuous aggregate data (TimescaleDB feature)
     */
    public function getContinuousAggregates(
        int $stationId, 
        Carbon $from, 
        Carbon $to
    ): Collection {
        // This assumes you've created a continuous aggregate view
        // See migration below for creating the view
        return collect(DB::select("
            SELECT 
                time_bucket,
                avg_load,
                max_load,
                min_load,
                avg_frequency
            FROM power_data_hourly_agg 
            WHERE power_station_id = ? 
            AND time_bucket BETWEEN ? AND ?
            ORDER BY time_bucket DESC
        ", [$stationId, $from, $to]));
    }

    /**
     * Get downsample data for charts/graphs
     */
    public function getDownsampledData(
        int $stationId,
        Carbon $from,
        Carbon $to,
        int $maxPoints = 100
    ): Collection {
        // Calculate appropriate time bucket based on date range and max points
        $totalMinutes = $from->diffInMinutes($to);
        $bucketMinutes = max(1, intval($totalMinutes / $maxPoints));
        $interval = $bucketMinutes . ' minutes';

        return collect(DB::select("
            SELECT 
                time_bucket(?, captured_at) AS time_bucket,
                AVG(load) as load,
                AVG(frequency) as frequency
            FROM power_data 
            WHERE power_station_id = ? 
            AND captured_at BETWEEN ? AND ?
            GROUP BY time_bucket 
            ORDER BY time_bucket ASC
        ", [$interval, $stationId, $from, $to]));
    }


    // public function save($data)
    // {
    //     $powerData = [
    //         "power_station_id" => $data['powerStationId'],
    //         "load" => $data['load'],
    //         "frequency" => $data['frequency'],
    //         "captured_at" => $data['capturedAt']
    //     ];
    //     return PowerData::firstOrCreate($powerData);
    // }
}

?>