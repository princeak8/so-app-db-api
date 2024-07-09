<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use App\Exports\LoadDropExport;

use App\Http\Resources\LoadDropResource;

use App\Services\LoadDropService;
use App\Services\PowerStationService;

use App\Http\Requests\LoadDrop;
use App\Http\Requests\AcknowledgeLoadDrop;
use App\Http\Requests\AcknowledgeStationLoadDrop;

use App\Utilities;

class LoadDropController extends Controller
{
    private $loadDropService;
    private $stationService;

    public function __construct()
    {
        $this->loadDropService = new LoadDropService;
        $this->stationService = new PowerStationService;
    }

    public function save(LoadDrop $request)
    {
        try{
            $data = $request->validated();
            $station = $this->stationService->powerStationByIdentifier($data['powerStationId']);
            $data['powerStationId'] = $station->id;
            $loadDrop = $this->loadDropService->save($data);
            return Utilities::okay(new LoadDropResource($loadDrop));
        }catch(\Exception $e) {
            return Utilities::error($e);
        }
    }

    public function acknowledge(AcknowledgeLoadDrop $request)
    {
        try{
            $data = $request->validated();
            $this->loadDropService->loadDropObj = $this->loadDropService->loadDrop($data['id']);
            if($this->loadDropService->loadDropObj) {
                $loadDrop = $this->loadDropService->acknowledge($data);
                return Utilities::okay(new LoadDropResource($loadDrop));
            }else{
                return Utilities::error402('LoadDrop was not found');
            }
        }catch(\Exception $e) {
            return Utilities::error($e);
        }
    }   

    public function acknowledgeStation(AcknowledgeStationLoadDrop $request)
    {
        try{
            $data = $request->validated();
            $station = $this->stationService->powerStationByIdentifier($data['identifier']);
            if($station) {
                $loadDrops = $this->loadDropService->stationLoadDrops($station->id);
                if($loadDrops->count() > 0) {
                    $data['loadDrops'] = $loadDrops;
                    $this->loadDropService->acknowledgeStation($data);
                }
                return utilities::okay();
            }else{
                return Utilities::error402('Power Station was not found');
            }
        }catch(\Exception $e) {
            return Utilities::error($e);
        }
    }

    public function latest()
    {
        try{
            $loadDrops = $this->loadDropService->latestLoadDrops();
            return Utilities::okay(LoadDropResource::collection($loadDrops));
        }catch(\Exception $e) {
            return Utilities::error($e);
        }
    }

    public function getRange(Request $request)
    {
        try{
            $data = $request->all();
            if(isset($data['start'])) {
                $loadDrops = (isset($data['group'])) ? $this->loadDropService->range($data, true) : $this->loadDropService->range($data);
                return Utilities::okay(LoadDropResource::collection($loadDrops));
            }else{
                return Utilities::error402('Start date is compulsory');
            }
        }catch(\Exception $e) {
            return Utilities::error($e);
        }
    }

    public function downloadRange(Request $request)
    {
        try{
            $data = $request->all();
            if(isset($data['start'])) {
                $loadDrops = (isset($data['group'])) ? $this->loadDropService->range($data, true) : $this->loadDropService->range($data);
                return Excel::download(new LoadDropExport($loadDrops), 'loadDroap Report '.$data['start']);
            }else{
                return Utilities::error402('Start date is compulsory');
            }
        }catch(\Exception $e) {
            return Utilities::error($e);
        }
    }

}
