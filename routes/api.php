<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PowerDataController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::options('{any}', function (Request $request) {
    return response('', 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin')
        ->header('Access-Control-Max-Age', '86400');
})->where('any', '.*');

Route::group(['prefix' => '/load_drop',], function () {
    Route::post('/save', 'LoadDropController@save');
    Route::post('/acknowledge', 'LoadDropController@acknowledge');
    Route::post('/acknowledge_station', 'LoadDropController@acknowledgeStation');
    Route::get('/latest', 'LoadDropController@latest');
    Route::get('/range', 'LoadDropController@getRange');
    Route::get('/download_range', 'LoadDropController@downloadRange');
    // Route::post('/acknowledged', 'LoadDropController@save');
    // Route::post('/un_acknowldeged', 'LoadDropController@save');
});

Route::group(['prefix' => '/power_data',], function () {
    Route::post('/save', [PowerDataController::class, 'save']);
});
