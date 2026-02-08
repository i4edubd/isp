<?php

use App\Http\Controllers\BlackListRemoveController;
use App\Http\Controllers\RrdGraphController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/black-lists/{username}', [BlackListRemoveController::class, 'destroy'])
    ->name('black-lists.destroy')
    ->where([
        'username' => '.*',
    ]);

Route::get('/rrd/create', [RrdGraphController::class, 'create'])
    ->name('rrddb.create');

Route::get('/rrd/image', [RrdGraphController::class, 'index'])
    ->name('rrd.index');

Route::get('/rrd/delete', [RrdGraphController::class, 'destroy'])
    ->name('rrd.destroy');
