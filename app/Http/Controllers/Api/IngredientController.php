<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\Recipe;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Ingredient::all();

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $ingredient->load('recipes');

    }

    /**
     * Search the specified resource in storage.
     */
    public function search(Request $request)
    {
        $request->validate([
            'ingredients'   => 'required|array',
            'ingredients.*' => 'string',
        ]);

        $names = $request->ingredients;
        $count = count($nombres);

        $recipes = Recipe::with(['ingredients', 'steps', 'user'])
            ->whereHas('ingredients', function ($query) use ($names) {
                $query->whereIn('name', $names);
            }, '>=', $count)
            ->get();

        return response()->json($recipes);
    }

} 