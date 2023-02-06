<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SolarController;

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

Route::post('register', [SolarController::class, 'register']);
Route::post('login', [SolarController::class, 'login']);
Route::get('logout', [SolarController::class, 'logout']);
Route::get('getData', [SolarController::class, 'getData'])->middleware('jwt');
