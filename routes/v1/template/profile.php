
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\template\ProfileController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    Route::post('/profiles-users/picture', [ProfileController::class, 'updatePicture']);
    Route::post('/profiles-users', [ProfileController::class, 'update']);
});
Route::prefix('v1')->middleware(["multiAuthorized:" . 'user:api,organization:api,donor:api'])->group(function () {
    Route::delete('/profiles', [ProfileController::class, 'delete']);
    Route::post('/profiles/change-password', [ProfileController::class, 'changePassword']);
});
Route::prefix('v1')->middleware(["authorized:" . 'organization:api'])->group(function () {
    Route::post('/profiles-organizations/picture', [ProfileController::class, 'updateOrganizationPicture']);
    Route::put('/profiles-organizations', [ProfileController::class, 'updateOrganization']);
});
Route::prefix('v1')->middleware(["authorized:" . 'donor:api'])->group(function () {
    Route::post('/profiles-donors/picture', [ProfileController::class, 'updateDonorPicture']);
    Route::put('/profiles-donors', [ProfileController::class, 'updateOrganization']);
});
