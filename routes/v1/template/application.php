
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\template\ApplicationController;

Route::prefix('v1')->group(function () {
    Route::get('/lang/{locale}', [ApplicationController::class, 'changeLocale']);
    Route::get('/locales/{lang}/{namespace}', [ApplicationController::class, 'getTranslations']);
    Route::get('/fonts/{filename}', [ApplicationController::class, "fonts"]);

    Route::get('/countries', [ApplicationController::class, "countries"]);
    Route::get('/provinces/{id}', [ApplicationController::class, "provinces"]);
    Route::get('/districts/{id}', [ApplicationController::class, 'districts']);
    Route::get('/genders', [ApplicationController::class, "genders"]);
    Route::get('/nationalities', [ApplicationController::class, "nationalities"]);
    Route::get('/nid/types', [ApplicationController::class, "nidTypes"]);
    Route::get('/currencies', [ApplicationController::class, "currencies"]);
});
Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
    // Applications
    Route::get('/applications', [ApplicationController::class, "applications"])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_application->value . ',' . 'view']);
    Route::put('/applications', [ApplicationController::class, "updateApplication"])->middleware(["userHasSubPermission:" . PermissionEnum::configurations->value . "," . SubPermissionEnum::configurations_application->value . ',' . 'edit']);
});
