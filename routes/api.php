<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SampleGeoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::middleware('gzip')->get('/v1', function (Request $request) {
//     Route::get('/latest_position', [SampleGeoController::class, 'latestPosition']);
//     Route::get('/previous_position', [SampleGeoController::class, 'previousPosition']);
// });
Route::get('latest_position', [SampleGeoController::class, 'latestPosition']);
Route::get('previous_position', [SampleGeoController::class, 'previousPosition']);
