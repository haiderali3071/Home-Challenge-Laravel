<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\PreferenceController;
use App\Http\Controllers\API\NewsController;

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

Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);

Route::middleware('auth:api')->group( function () {
    Route::post('set-preferences', [PreferenceController::class, 'setPreferences']);
    Route::get('get-preferences', [PreferenceController::class, 'getPreferences']);
    Route::get('get-top-stories', [NewsController::class, 'getTopStories']);
    Route::get('get-categories', [NewsController::class, 'getCategories']);
    Route::get('get-sources', [NewsController::class, 'getSources']);
    Route::post('get-filtered-news', [NewsController::class, 'getFilteredNews']);
    Route::post('get-personalized-news-feed', [NewsController::class, 'getPersonalizedNewsFeed']);
});