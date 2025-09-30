<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
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

Route::post('/login', [AuthController::class, 'login']);


Route::middleware(['api', 'wp.auth'])->group(function () {
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/priority', [PostController::class, 'indexByPriority']); // ✅ Move this up
    Route::get('/posts/{id}', [PostController::class, 'show']);      // Show single post
    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

     Route::post('/posts/{id}/priority', [PostController::class, 'setPriority']);
     Route::get('/posts/{id}/priority', [PostController::class, 'getPriority']);

   // Route::get('/posts/priority', [PostController::class, 'indexByPriority']);


});