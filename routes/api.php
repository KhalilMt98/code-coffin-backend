<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SourceCodesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


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

Route::prefix('source-codes')->group(function () {
    Route::get('/', [SourceCodesController::class, 'getAllSourceCodes']);
    Route::get('/{id}', [SourceCodesController::class, 'getSourceCode']);
    Route::post('/', [SourceCodesController::class, 'createSourceCode']);
    Route::put('/{id}', [SourceCodesController::class, 'updateSourceCode']);
    Route::delete('/{id}', [SourceCodesController::class, 'deleteSourceCode']);
});