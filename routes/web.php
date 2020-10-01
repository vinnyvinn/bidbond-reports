<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return response()->json(['message' => 'Reports home']);
});


Route::group(['prefix'=>'reports'],function () {
    Route::resource('dashboard', 'DashBoardController');
    Route::get('companies', 'ReportsController@index');
    Route::get('bidbonds', 'ReportsController@getBidbonds');
});
Route::get('/home', function () {
    return view('welcome');

});
