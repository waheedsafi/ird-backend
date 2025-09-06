
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\template\FaqController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
  Route::get('/faqs', [FaqController::class, "index"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_faqs->value . ',' . 'view']);
  Route::post('/faqs', [FaqController::class, "store"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_faqs->value . ',' . 'add']);

  Route::get('/faqs/{id}', [FaqController::class, "edit"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_faqs->value . ',' . 'view']);
  Route::delete('/faqs/{id}', [FaqController::class, "destroy"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_faqs->value . ',' . 'view']);
  Route::put('/faqs', [FaqController::class, "update"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_faqs->value . ',' . 'edit']);
});
Route::prefix('v1')->group(function () {
  Route::get('/faqs-public', [FaqController::class, "indexPublic"]);
});
