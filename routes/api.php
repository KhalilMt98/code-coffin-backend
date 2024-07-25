<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
});
Route::group([
    "middleware" => "auth.user",
    "prefix" => "users",
    "controller" => UserController::class
], function () {
    Route::get('/', 'getAllUsers');
    Route::get('/{id}', 'getUser');
    Route::put('/{id}', 'updateUser');
    Route::delete('/{id}', 'deleteUser');
});
