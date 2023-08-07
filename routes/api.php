<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MegaController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('forgotpass', [UserController::class, 'forgotPassword']);
Route::get('resetpass', [UserController::class, 'resetpass']);
Route::post('updatepassword', [UserController::class, 'updatepass']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();


});
Route::group(['middleware' => 'auth:sanctum'], function (){
    Route::post('apikey', [MegaController::class, 'apikey']);
    Route::post('pricing', [MegaController::class, 'pricing']);
    Route::post("translator", [MegaController::class, 'translator']);
});
