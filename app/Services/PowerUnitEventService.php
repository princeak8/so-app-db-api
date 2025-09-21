<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

use App\Models\PowerUnitEvent;

class PowerUnitEventService
{
    /**
     * Get recent events for a station
     */
    public function getRecentEvents(int $stationId, int $hours = 24): Collection
    {
        $since = Carbon::now()->subHours($hours);
        
        return PowerUnitEvent::where('power_station_id', $stationId)
                             ->where('created_at', '>=', $since)
                             ->orderBy('created_at', 'desc')
                             ->get();
    }

    /**
     * Get events by type with time bucketing
     */
    public function getEventsByType(
        int $stationId, 
        string $eventType, 
        Carbon $from, 
        Carbon $to
    ): Collection {
        return PowerUnitEvent::where('power_station_id', $stationId)
                             ->where('event', $eventType)
                             ->whereBetween('created_at', [$from, $to])
                             ->orderBy('created_at', 'desc')
                             ->get();
    }

    /**
     * Get event statistics
     */
    public function getEventStatistics(
        int $stationId, 
        Carbon $from, 
        Carbon $to
    ): Collection {
        return collect(DB::select("
            SELECT 
                event,
                COUNT(*) as event_count,
                AVG(load) as avg_load_during_event,
                MIN(created_at) as first_occurrence,
                MAX(created_at) as last_occurrence
            FROM power_unit_events 
            WHERE power_station_id = ? 
            AND created_at BETWEEN ? AND ?
            GROUP BY event 
            ORDER BY event_count DESC
        ", [$stationId, $from, $to]));
    }

    /**
     * Save event with automatic timestamp
     */
    public function logEvent(array $eventData): PowerUnitEvents
    {
        return PowerUnitEvent::create([
            'power_station_id' => $eventData['powerStationId'],
            'power_unit_id' => $eventData['powerUnitId'],
            'event' => $eventData['event'],
            'load' => $eventData['load'],
            'prev_load' => $eventData['prevLoad'],
            'reference_load' => $eventData['referenceLoad'],
            'frequency' => $eventData['frequency']
        ]);
    }
}