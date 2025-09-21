<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

use App\Models\PowerUnitData;

class PowerUnitDataService
{

    public function powerUnitData($id)
    {
        return PowerUnitData::find($id);
    }

    public function stationUnitData($stationId)
    {
        return PowerData::where('power_station_id', $stationId)->get();
    }

    // public function UnitData($powerUnitId)
    // {
    //     return PowerData::where('power_unit_id', $powerUnitId)->get();
    // }
    /**
     * Get data for specific power unit
     */
    public function unitData(int $unitId, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $query = PowerUnitData::where('power_unit_id', $unitId);
        
        if ($from && $to) {
            $query->whereBetween('captured_at', [$from, $to]);
        }
        
        return $query->orderBy('captured_at', 'desc')->get();
    }

    /**
     * Get latest data for all units in a station
     */
    public function latestStationUnitsData(int $stationId): Collection
    {
        return collect(DB::select("
            SELECT DISTINCT ON (power_unit_id) 
                   id, power_station_id, power_unit_id, 
                   mw, kv, a, mx, frequency, captured_at
            FROM power_unit_data 
            WHERE power_station_id = ?
            ORDER BY power_unit_id, captured_at DESC
        ", [$stationId]));
    }

    public function powerDataByStationAndCapturedAt($stationId, $capturedAt)
    {
        return LoadDrop::where('power_station_id', $stationId)->where('captured_at', $capturedAt)->get();
    }

    public function latestPowerData($limit=10)
    {
        $limit = env('LATEST_LOAD_DROPS_LIMIT', $limit);
        return PowerUnitData::select(DB::raw('DISTINCT ON (captured_at) *'))
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

    /**
     * Get aggregated unit performance data
     */
    public function getUnitPerformance(
        int $unitId, 
        Carbon $from, 
        Carbon $to, 
        string $interval = '1 hour'
    ): Collection {
        return collect(DB::select("
            SELECT 
                time_bucket(?, captured_at) AS time_bucket,
                AVG(mw) as avg_mw,
                MAX(mw) as max_mw,
                MIN(mw) as min_mw,
                AVG(kv) as avg_kv,
                AVG(a) as avg_amperage,
                AVG(frequency) as avg_frequency,
                COUNT(*) as readings_count
            FROM power_unit_data 
            WHERE power_unit_id = ? 
            AND captured_at BETWEEN ? AND ?
            GROUP BY time_bucket 
            ORDER BY time_bucket DESC
        ", [$interval, $unitId, $from, $to]));
    }

    /**
     * Get real-time unit monitoring data
     */
    public function getRealtimeUnitsData(int $stationId, int $minutes = 5): Collection
    {
        $since = Carbon::now()->subMinutes($minutes);
        
        return PowerUnitData::where('power_station_id', $stationId)
                           ->where('captured_at', '>=', $since)
                           ->orderBy('power_unit_id')
                           ->orderBy('captured_at', 'desc')
                           ->get();
    }

    /**
     * Bulk save unit data
     */
    public function bulkSave(array $dataArray): bool
    {
        $insertData = [];
        
        foreach ($dataArray as $data) {
            $insertData[] = [
                "power_station_id" => $data['powerStationId'],
                "power_unit_id" => $data['powerUnitId'],
                "power_data_id" => $data['powerDataId'] ?? null,
                "mw" => $data['mw'],
                "kv" => $data['kv'],
                "a" => $data['a'],
                "mx" => $data['mx'],
                "frequency" => $data['frequency'],
                "captured_at" => $data['capturedAt'],
                "created_at" => now(),
                "updated_at" => now()
            ];
        }

        // Use chunking for large datasets
        $chunks = array_chunk($insertData, 1000);
        
        DB::transaction(function () use ($chunks) {
            foreach ($chunks as $chunk) {
                PowerUnitData::insert($chunk);
            }
        });

        return true;
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

    /**
     * Single record save
     */
    public function save(array $data): PowerUnitData
    {
        $unitData = [
            "power_station_id" => $data['powerStationId'],
            "power_unit_id" => $data['powerUnitId'],
            "power_data_id" => $data['powerDataId'] ?? null,
            "mw" => $data['mw'],
            "kv" => $data['kv'],
            "a" => $data['a'],
            "mx" => $data['mx'],
            "frequency" => $data['frequency'],
            "captured_at" => $data['capturedAt']
        ];

        return PowerUnitData::create($unitData);
    }

    /**
     * Get efficiency analysis data
     */
    public function getEfficiencyAnalysis(
        int $unitId, 
        Carbon $from, 
        Carbon $to
    ): Collection {
        return collect(DB::select("
            SELECT 
                time_bucket('1 day', captured_at) AS day,
                AVG(mw) as avg_power,
                AVG(kv) as avg_voltage,
                AVG(a) as avg_current,
                AVG(mw / NULLIF(kv * a * 1.732, 0)) as avg_efficiency,
                STDDEV(frequency) as frequency_stability
            FROM power_unit_data 
            WHERE power_unit_id = ? 
            AND captured_at BETWEEN ? AND ?
            GROUP BY day 
            ORDER BY day DESC
        ", [$unitId, $from, $to]));
    }
}

?>