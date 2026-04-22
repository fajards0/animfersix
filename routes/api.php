<?php

use App\Http\Controllers\Api\CompleteAnimeController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\Mobile\AnimeDetailController;
use App\Http\Controllers\Api\Mobile\CatalogController;
use App\Http\Controllers\Api\Mobile\HomeFeedController;
use App\Http\Controllers\Api\ScraperHealthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/home', HomeController::class);
Route::get('/complete-anime', CompleteAnimeController::class);
Route::get('/scraper-health', ScraperHealthController::class);

Route::prefix('mobile')->group(function () {
    Route::get('/home', HomeFeedController::class);
    Route::get('/catalog', CatalogController::class);
    Route::get('/anime/{animeId}', AnimeDetailController::class)->where('animeId', '.*');
});
