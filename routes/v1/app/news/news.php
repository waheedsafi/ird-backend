
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\app\news\NewsController;

Route::prefix('v1')->group(function () {
  Route::get('/news/public', [NewsController::class, "publicNewses"]);
  Route::get('/newses/high', [NewsController::class, "highPriorityNews"]);
  Route::get('/newses/latest', [NewsController::class, "latestNews"]);
  Route::get('/public/news/{id}', [NewsController::class, "publicNews"]);
});
Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
  Route::get('/newses', [NewsController::class, "index"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_news->value . ',' . 'view']);
  Route::get('/newses/{id}', [NewsController::class, "edit"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_news->value . ',' . 'view']);
  Route::post('newses', [NewsController::class, 'store'])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_news->value . ',' . 'add']);
  Route::put('/newses', [NewsController::class, "update"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_news->value . ',' . 'edit']);

  Route::delete('/newses/{id}', [NewsController::class, "destroy"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_news->value . ',' . 'delete']);
});
