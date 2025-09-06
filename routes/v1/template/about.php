
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\template\AboutController;




Route::prefix('v1')->group(function () {
  Route::get('/about/office', [AboutController::class, "office"]);
  Route::get('/about/staffs', [AboutController::class, "staffs"]);
  // Route::get('/public/sliders', [AboutController::class, 'publicSliders']);
});
Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
  Route::get('/about/director', [AboutController::class, "director"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_director->value . ',' . 'view']);
  Route::get('/about/manager', [AboutController::class, "manager"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_manager->value . ',' . 'view']);
  Route::get('/about/technical-support', [AboutController::class, "technicalSupport"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_technical->value . ',' . 'view']);
  Route::get('/about/office-detail', [AboutController::class, "editOffice"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_office->value . ',' . 'view']);

  Route::post('/about/technical-support', [AboutController::class, 'storeTechnicalSupport'])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_technical->value . ',' . 'add']);
  Route::put('/about/technical-support', [AboutController::class, 'updateTechnicalSupport'])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_technical->value . ',' . 'add']);

  Route::post('/about/manager', [AboutController::class, 'storeManager'])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_manager->value . ',' . 'add']);
  Route::put('/about/manager', [AboutController::class, 'updateManager'])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_manager->value . ',' . 'edit']);

  Route::post('/about/director', [AboutController::class, 'storeDirector'])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_director->value . ',' . 'add']);
  Route::put('/about/director', [AboutController::class, 'updateDirector'])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_director->value . ',' . 'edit']);

  Route::post('/about/office', [AboutController::class, 'storeOffice'])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_office->value . ',' . 'add']);
  Route::put('/about/office', [AboutController::class, 'updateOffice'])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_office->value . ',' . 'edit']);
});
