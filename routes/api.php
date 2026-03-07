<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RecipesController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\RecipeStepController;
use App\Http\Controllers\Api\CommentController;

// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/recipes', [RecipesController::class, 'index']);
Route::get('/recipes/{recipe}', [RecipesController::class, 'show']);
Route::get('/recipes/{recipe}/steps', [RecipeStepController::class, 'index']);
Route::get('/ingredients', [IngredientController::class, 'index']);
Route::post('/ingredients/search', [IngredientController::class, 'search']);
Route::get('/recipes/{recipe}/comments', [CommentController::class, 'index']);


// Usuario auntenticado 
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/recipes', [RecipesController::class, 'store']);
    Route::put('/recipes/{recipe}', [RecipesController::class, 'update']);
    Route::delete('/recipes/{recipe}', [RecipesController::class, 'destroy']);
    Route::post('/recipes/{recipe}/comments', [CommentController::class, 'store']);
    Route::delete('/recipes/{recipe}/comments/{comment}', [CommentController::class, 'destroy']);
});

// ── SOLO ADMIN ────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'es.admin'])->group(function () {
    Route::get('/users', function () {
        return \App\Models\User::all();
    });
});