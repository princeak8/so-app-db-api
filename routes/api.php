<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
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
