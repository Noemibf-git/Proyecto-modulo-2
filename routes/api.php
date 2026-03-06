<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RecipesController;

// RUTAS PÚBLICAS
Route::post('/login', [AuthController::class, 'login']);
Route::get('/recipes', [RecipesController::class, 'index']);
Route::get('/recipes/{recipe}', [RecipesController::class, 'show']);

// USUARIO AUTENTICADO 
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/recipes', [RecipesController::class, 'store']);
    Route::put('/recipes/{recipe}', [RecipesController::class, 'update']);
    Route::delete('/recipes/{recipe}', [RecipesController::class, 'destroy']);
});

// ── SOLO ADMIN ────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'es.admin'])->group(function () {
    Route::get('/users', function () {
        return \App\Models\User::all();
    });
});