<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RecipesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Recipe::with('user')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Crear receta
        $data = $request->validate([
        'title' => 'required|string|max:35',
        'description'=>'nullable|string|max:250',
        'ingredients' => 'required|string|max:15',
        'steps' => 'required|string|max:250',
        ]);

        $recipe = $request->user()->recipes()->create($data);
        return response()->json(['id' => $recipe->id], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        return $recipe->load('user');

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Recipe $recipe)
    {
        if ($recipe->user_id !== Auth::id() && Auth::user()->role !== 'admin'){
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $data = $request -> validate([
        'title' => 'sometimes|required|string|max:35',
        'description'=>'nullable|string|max:250',
        'ingredients' => 'sometimes|required|string|max:15',
        'steps' => 'sometimes|required|string|max:250',
        ]);

       $recipe->update($data);
       return $recipe;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe)
    { 
        if ($recipe->user_id !== Auth::id() && Auth::user()->role !== 'admin'){
            return response()->json(['message' => 'No autorizado'], 403);
        }
        $recipe -> delete();
        return response()->json(
            ['message'=>'receta eliminada correctamente'],
            200
        );
    }
}
