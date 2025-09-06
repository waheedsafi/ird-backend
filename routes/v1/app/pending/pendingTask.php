
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\app\pending\PendingTaskController;

Route::prefix('v1')->middleware(["multiAuthorized:" . 'user:api,organization:api'])->group(function () {
  Route::post('pending-tasks/{id}', [PendingTaskController::class, 'storeWithContent']);
});
