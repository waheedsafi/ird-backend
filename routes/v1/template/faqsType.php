
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\template\FaqTypeController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
  Route::get('/faqs-types', [FaqTypeController::class, "index"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_faqs_type->value . ',' . 'view']);
  Route::get('/faqs-types/{id}', [FaqTypeController::class, "edit"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_faqs_type->value . ',' . 'view']);
  Route::delete('/faqs-types/{id}', [FaqTypeController::class, "destroy"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_faqs_type->value . ',' . 'delete']);
  Route::post('/faqs-types', [FaqTypeController::class, "store"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_faqs_type->value . ',' . 'add']);
  Route::put('/faqs-types', [FaqTypeController::class, "update"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_faqs_type->value . ',' . 'edit']);
});
