<?php

use App\Http\Controllers\API\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);

Route::group(['middleware' => 'authapi'], function () {
    Route::post('/logout', [LoginController::class, 'logout']);
});
