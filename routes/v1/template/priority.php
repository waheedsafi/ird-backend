
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\template\PriorityController;

Route::prefix('v1')->middleware(["authorized:" . 'user:api'])->group(function () {
  Route::get('/priorities', [PriorityController::class, "index"]);
});
