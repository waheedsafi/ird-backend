
<?php

use Illuminate\Support\Facades\Route;
use App\Enums\Permissions\PermissionEnum;
use App\Enums\Permissions\SubPermissionEnum;
use App\Http\Controllers\v1\app\news\NewsTypeController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
  Route::get('/news-types', [NewsTypeController::class, "index"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_news_type->value . ',' . 'view']);
  Route::post('/news-types', [NewsTypeController::class, "store"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_news_type->value . ',' . 'add']);
  Route::put('/news-types', [NewsTypeController::class, "update"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_news_type->value . ',' . 'edit']);

  Route::get('/news-types/{id}', [NewsTypeController::class, "edit"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_news_type->value . ',' . 'view']);
  Route::delete('/news-types/{id}', [NewsTypeController::class, "destroy"])->middleware(["userHasSubPermission:" . PermissionEnum::about->value . "," . SubPermissionEnum::about_news_type->value . ',' . 'delete']);
});
