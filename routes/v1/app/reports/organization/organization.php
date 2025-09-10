
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\app\reports\organization\OrganizationReportsController;

Route::get('/organizations/report', [OrganizationReportsController::class, "generateReport"]);

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
  // view
  Route::get('/organizations/statistics', [OrganizationReportsController::class, "generateReport"])->middleware(["userHasMainPermission:" . PermissionEnum::organizations->value . ',' . 'view']);
});
