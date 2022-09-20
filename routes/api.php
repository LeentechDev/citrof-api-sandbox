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
        Route::get('getToken', [App\Http\Controllers\AuthController::class, 'getToken'])->name('getToken');
        Route::post('generate-token', [App\Http\Controllers\AuthController::class, 'generateToken'])->name('generateToken');
        Route::get('events', [App\Http\Controllers\EventController::class, 'getEvents'])->name('getEvents');
    });

});

/* ========== for Facilito project purpose ========= */
/* 
    add here the custom api 
*/
Route::group(['prefix' => 'v2'], function () {

    Route::group(['prefix' => '', 'middleware' => ['authorization']], function () {
        Route::post('/generate-token', 'EntregoController@generateToken2')->name('generateToken2');
    });

    
});