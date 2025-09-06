<?php

use App\Http\Controllers\v1\auth\OrganizationAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth-organization', [OrganizationAuthController::class, 'login']);
});

Route::prefix('v1')->middleware(["authorized:" . 'organization:api'])->group(function () {
    Route::post('/organization/auth-logout', [OrganizationAuthController::class, 'logout']);
    Route::get('/auth-organization', [OrganizationAuthController::class, 'user']);
    Route::post('/profile/change-password', [OrganizationAuthController::class, 'changePassword']);
});
