<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return "Chat app v1";
});

Route::post('/register', [AuthController::class, 'register']);
