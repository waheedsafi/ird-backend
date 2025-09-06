
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\template\SlideshowController;

Route::prefix('v1')->group(function () {
  Route::get('/slideshows/public', [SlideshowController::class, "publicIndex"]);
});
Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
  Route::get('/slideshows', [SlideshowController::class, "index"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_slideshow->value . ',' . 'view']);
  Route::get('/slideshows/{id}', [SlideshowController::class, "edit"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_slideshow->value . ',' . 'view']);

  Route::post('/slideshows', [SlideshowController::class, 'store'])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_slideshow->value . ',' . 'add']);
  Route::put('/slideshows', [SlideshowController::class, 'update'])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_slideshow->value . ',' . 'edit']);
});
