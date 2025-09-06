<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\auth\AuthController;

Route::prefix('v1')->group(function () {
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
});
