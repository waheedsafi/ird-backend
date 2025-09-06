
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\app\file\FileController;



Route::prefix('v1')->middleware(["multiAuthorized:" . 'user:api,organization:api'])->group(function () {
  Route::post('checklist/file/upload', [FileController::class, 'checklistUploadFile'])->withoutMiddleware('throttle');
  Route::post('files/single/upload', [FileController::class, 'singleFileUpload'])->withoutMiddleware('throttle');
  Route::post('single/checklist/file/upload', [FileController::class, 'singleChecklistFileUpload'])->withoutMiddleware('throttle');
  Route::post('single/checklist/file/upload/no-pending', [FileController::class, 'singleChecklistFileUploadNoPending'])->withoutMiddleware('throttle');
});
