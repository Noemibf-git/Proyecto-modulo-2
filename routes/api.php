<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::post('/login', function (Request $request){
    if()
})







Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
