<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\RecipeStep;

class RecipeStepController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $recipe->steps;

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
     return $step;

    }
}
