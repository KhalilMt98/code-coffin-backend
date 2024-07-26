<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessagesController;
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

Route::group([
    "middleware" => "auth.user",
    "prefix" => "source-codes",
    "controller" => SourceCodesController::class
],function () {
    Route::get('/','getAllSourceCodes');
    Route::get('/{id}','getSourceCode');
    Route::post('/', 'createSourceCode');
    Route::put('/{id}',  'updateSourceCode');
    Route::delete('/{id}',  'deleteSourceCode');
});
Route::group([
    "middleware" => "auth.user",
    "prefix" => "messages",
    "controller" => MessagesController::class ],function (){
        Route::get('/',  'getMessages');
        Route::post('/',  'createMessage');
        Route::put('/{id}','updateMessage');
        Route::delete('/{id}', 'deleteMessage');
    }
);