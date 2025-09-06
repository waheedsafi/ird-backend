
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Http\Controllers\v1\app\projects\ProjectPdfController;

Route::prefix('v1')->middleware(["authorized:" . 'organization:api'])->group(function () {
  Route::get('projects/unsigned/mou/{id}', [ProjectPdfController::class, 'generateMou'])->middleware(["userHasMainPermission:" . PermissionEnum::projects->value . ',' . 'view']);
});
