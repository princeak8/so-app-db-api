<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\PowerData;

use App\Services\PowerDataService;
use App\Services\PowerStationService;
use App\Services\PowerUnitService;

use App\Utilities;

class PowerDataController extends Controller
{
    private $powerDataService;
    private $powerStationService;
    private $powerUnitService;

    public function __construct()
    {
        $this->powerDataService = new PowerDataService;
        $this->powerStationService = new PowerStationService;
        $this->powerUnitService = new PowerUnitService;
    }


    public function save(PowerData $request)
    {
        // try{
            // Utilities::logStuff($request->all());
            $powerDataBatch = [];
            $unitDataBatch = [];
            $processedCount = 0;

            // Prepare bulk data
            $index = 0;
            foreach ($request->data as $key=>$stationData) {
                $timestamp = Carbon::parse($stationData['capturedAt']);

                // Prepare power data for bulk insert
                $powerStation = $this->powerStationService->powerStationByIdentifier($stationData['powerStationId']);
                if($powerStation) {
                    $powerDataBatch[] = [
                        'powerStationId' => $powerStation->id,
                        'load' => $stationData['load'],
                        'frequency' => $stationData['frequency'],
                        'capturedAt' => $timestamp
                    ];

                    // Prepare unit data for bulk insert if provided
                    if (isset($stationData['unitsData']) && is_array($stationData['unitsData'])) {
                        foreach ($stationData['unitsData'] as $unitData) {
                            // dd($unitData);
                            // Utilities::logStuff("station Data");
                            // Utilities::logStuff($stationData);
                            $powerUnit = $this->powerUnitService->getByIdentifier($unitData['id']);
                            if($powerUnit) {
                                $powerDataBatch[$index]['unitsData'][] = [
                                    'powerStationId' => $powerStation->id,
                                    'powerUnitId' => $powerUnit->id,
                                    'mw' => $unitData['mw'],
                                    'kv' => $unitData['kv'],
                                    'a' => $unitData['a'],
                                    'mx' => $unitData['mx'],
                                    'frequency' => $unitData['frequency'],
                                    'capturedAt' => $timestamp
                                ];
                            }
                        }
                    }
                    $index = $index + 1;
                }

                $processedCount++;
            }

            // Bulk save data
            DB::transaction(function () use ($powerDataBatch, $unitDataBatch) {
                if (!empty($powerDataBatch)) {
                    // dd($powerDataBatch[0]);
                    $this->powerDataService->bulkSave($powerDataBatch);
                }
                // if (!empty($unitDataBatch)) {
                //     $this->powerUnitService->bulkSave($unitDataBatch);
                // }
            });

            // Log successful bulk injection
            Log::info('Bulk power data injection successful', [
                'stations_processed' => $processedCount,
                'power_records' => count($powerDataBatch)
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Bulk data processed successfully',
                'summary' => [
                    'stations_processed' => $processedCount,
                    'power_data_records' => count($powerDataBatch),
                ],
                'timestamp' => now()->toISOString()
            ], 201);

        // }catch(\Exception $e) {
        //     Log::error('Bulk power data injection failed', [
        //         'error' => $e->getMessage(),
        //         'trace' => $e->getTraceAsString(),
        //         'request_data_count' => count($request->data ?? []),
        //     ]);
        //     return Utilities::error($e);
        // }
    }
}
