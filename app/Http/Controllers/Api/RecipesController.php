<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RecipesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Recipe::with('user', 'ingredients', 'steps')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Crear receta
        $data = $request->validate([
            'title'                         => 'required|string|max:35',
            'description'                   => 'nullable|string|max:250',
            'imagen'                        => 'nullable|string',
            'ingredients'                   => 'required|array',
            'ingredients.*.name'            => 'required|string',
            'ingredients.*.quantity'        => 'required|numeric',
            'ingredients.*.unit'            => 'nullable|string',
            'steps'                         => 'required|array',
            'steps.*.step_number'           => 'required|integer',
            'steps.*.description'           => 'required|string',
        ]);

        $recipe = $request->user()->recipes()->create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'imagen'      => $data['imagen'] ?? null,
        ]);

        //Ingredientes
        foreach ($data['ingredients'] as $ingredientData) {
            $ingredient = Ingredient::firstOrCreate(['name' => $ingredientData['name']]);
            //Uso el método attach para unir ingrediente y receta
            $recipe->ingredients()->attach($ingredient->id, [
                'quantity' => $ingredientData['quantity'],
                'unit'     => $ingredientData['unit'] ?? null,
            ]);
        }

        //Pasos
        $recipe->steps()->createMany($data['steps']);
        return response()->json($recipe->load(['ingredients' , 'steps']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe)
    {
        return $recipe->load('user', 'ingredients','steps');

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
        'imagen'=> 'nullable|string',
        'ingredients'            => 'sometimes|array',
        'ingredients.*.name'     => 'required|string',
        'ingredients.*.quantity' => 'required|numeric',
        'ingredients.*.unit'     => 'nullable|string',
        'steps'                  => 'sometimes|array',
        'steps.*.step_number'    => 'required|integer',
        'steps.*.description'    => 'required|string',
        ]);

       $recipe->update(([
        'title'       => $data['title'] ?? $recipe->title,
        'description' => $data['description'] ?? $recipe->description,
        'imagen'      => $data['imagen'] ?? $recipe->imagen,
       ]));
        if (isset($data['ingredients'])) {
                $recipe->ingredients()->detach(); 
                foreach ($data['ingredients'] as $ingredientData) {
                    $ingredient = Ingredient::firstOrCreate(['name' => $ingredientData['name']]);
                    $recipe->ingredients()->attach($ingredient->id, [
                        'quantity' => $ingredientData['quantity'],
                        'unit'     => $ingredientData['unit'] ?? null,
                    ]);
                }
            }

        if (isset($data['steps'])) {
        $recipe->steps()->delete();
        $recipe->steps()->createMany($data['steps']);
    }



       return $recipe->load(['ingredients', 'steps']);
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
