<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\hardware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HardwareController;
use App\Http\Controllers\reportController;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\takeFotoController;
use App\Http\Controllers\kaApiController;

Route::post('/chat', [kaApiController::class, 'chat']);

route::post('/register', [AuthController::class , 'register']);
route::post('login',[AuthController::class , 'login']);



Route::post('/sensor/{id}', [HardwareController::class, 'sensor']);

Route::get('/sensor/{id}', [HardwareController::class, 'showSensor']);

Route::get('/sensor', [HardwareController::class, 'allSensor']);

Route::get('/hardware/{id}', [HardwareController::class, 'getStatus']);

Route::put('/hardware/{id}', [HardwareController::class, 'update']);

Route::post('/upload', [takeFotoController::class, 'store']);
Route::get('/photos', [takeFotoController::class, 'index']);
Route::get('/usn' ,[AuthController::class , 'username']);

Route::post('/report' , [reportController::class , 'store']);
Route::get('/report' , [reportController::class , 'index']);