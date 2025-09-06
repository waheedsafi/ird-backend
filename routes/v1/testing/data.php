
<?php

use App\Http\Controllers\TestingController;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::get('/books', [TestingController::class, "books"]);
    Route::get('/health-books', [TestingController::class, "healthBooks"]);
    Route::get('/sports-books', [TestingController::class, "sportBooks"]);
    Route::get('/foods-books', [TestingController::class, "foodBooks"]);
    Route::get('/about-us', [TestingController::class, "aboutUs"]);
    Route::get('/testing', [TestingController::class, "testing"]);
});
