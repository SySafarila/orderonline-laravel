<?php

use App\Http\Controllers\AbilityController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PokemonController;
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

// pokemons
Route::resource('/pokemons', PokemonController::class)->only(['index', 'show']);

// abilities
Route::resource('/abilities', AbilityController::class)->only(['index', 'show']);

// favorite
Route::get('/favorite/check/{name}', [FavoriteController::class, 'isFavorite'])->name('favorite.check');
Route::get('/favorite', [FavoriteController::class, 'index'])->name('favorite.index');
Route::post('/favorite', [FavoriteController::class, 'store'])->name('favorite.store');
Route::delete('/favorite/{name}', [FavoriteController::class, 'destroy'])->name('favorite.destroy');
