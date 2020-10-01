<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

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

Route::get('migrate',function (){
    Artisan::call('cache:clear');
    Artisan::call('migrate:fresh --force');
    Artisan::call('data:sync');
    return response()->json(["message"=>"reports migrate fresh success"]);
});

Route::get('bidbonds','BidbondController@index');
Route::get('bidbonds/summary','SummaryController@bidbond_summary');
Route::get('bidbonds/expired','BidbondController@expired');
Route::get('bidbonds/byRM','BidbondController@byRM');
Route::get('bidbonds/company-summary','RevenueController@break_down');

