<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
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

Route::apiResource('albums', AlbumController::class);
Route::apiResource('images', ImageController::class)->except('store');
Route::apiResource('tags', TagController::class);
Route::apiResource('users', UserController::class);

Route::get('albums/{album}/images', [ImageController::class, 'indexAlbum']);
Route::post('albums/{album}/images', [ImageController::class, 'store']);
