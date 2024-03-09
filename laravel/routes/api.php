<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OMDbController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('password/reset', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function() {

    Route::get('/me', function(Request $request) {
        return response($request->user()->ratings, 200);
    });

    Route::put('update/user', [UserController::class, 'update']);

    Route::get('movies', [OMDbController::class, 'movies']);
    Route::post('rate/movie', [OMDbController::class, 'rate']);
    Route::get('download/movie', [OMDbController::class, 'download']);
    Route::get('send/code', [OMDbController::class, 'sendCode']);
});