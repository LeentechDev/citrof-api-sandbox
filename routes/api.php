<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* ========== add here all the base api ========= */
Route::group(['prefix' => 'v1'], function () {

    Route::group(['prefix' => '', 'middleware' => ['authorization']], function () {
        Route::post('generate-token', [App\Http\Controllers\AuthController::class, 'generateToken'])->name('generateToken');
        Route::post('events/list', [App\Http\Controllers\EventController::class, 'getEvents'])->name('getEvents');
        Route::post('event/details', [App\Http\Controllers\EventController::class, 'getEvent'])->name('getEvent');
        Route::post('event/fights', [App\Http\Controllers\FightController::class, 'getEventFights'])->name('getEventFights');
        Route::post('fight/details', [App\Http\Controllers\FightController::class, 'getFight'])->name('geFight');
        Route::post('bets/betting-table', [App\Http\Controllers\BetController::class, 'getBettingTable'])->name('getBettingTable');
        Route::post('bets/list', [App\Http\Controllers\BetController::class, 'getBettingHistory'])->name('getBettingHistory');
    });
    // Route::group(['prefix' => 'player', 'middleware' => ['authorization']], function () {
    //     Route::post('/list','App\Http\Controllers\PlayerController@get')->name('player_list');
    //     Route::post('/details','App\Http\Controllers\PlayerController@details')->name('player_details');
    // });
    Route::group(['prefix' => 'player'], function () {
        Route::post('/list','App\Http\Controllers\PlayerController@index')->name('player_list');
        Route::post('/details','App\Http\Controllers\PlayerController@get')->name('player_details');
    });

});

/* 
    add here the custom api 
*/
Route::group(['prefix' => 'v2'], function () {
    Route::group(['prefix' => '', 'middleware' => ['authorization']], function () {
        Route::post('/generate-token', 'EntregoController@generateToken2')->name('generateToken2');
    });
});