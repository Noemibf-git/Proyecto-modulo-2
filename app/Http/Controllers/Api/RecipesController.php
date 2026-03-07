<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class RecipesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(path: '/api/recipes', summary: 'Listar todas las recetas', tags: ['Recetas'])]
    #[OA\Response(response: 200, description: 'Lista de recetas')]
    public function index()
    {
        return Recipe::with('user', 'ingredients', 'steps')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    #[OA\Post(path: '/api/recipes', summary: 'Crear una receta', tags: ['Recetas'], security: [['bearerAuth' => []]])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
        new OA\Property(property: 'title', type: 'string', example: 'Tortilla de patatas'),
        new OA\Property(property: 'description', type: 'string', example: 'Receta clásica'),
        new OA\Property(property: 'imagen', type: 'string', example: 'tortilla.jpg'),
    ]))]
    #[OA\Response(response: 201, description: 'Receta creada')]
    #[OA\Response(response: 401, description: 'No autenticado')]
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
    #[OA\Get(path: '/api/recipes/{id}', summary: 'Ver una receta', tags: ['Recetas'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Receta encontrada')]
    #[OA\Response(response: 404, description: 'No encontrada')]
    public function show(Recipe $recipe)
    {
        return $recipe->load('user', 'ingredients','steps');

    }

    /**
     * Update the specified resource in storage.
     */
    #[OA\Put(path: '/api/recipes/{id}', summary: 'Actualizar una receta', tags: ['Recetas'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Receta actualizada')]
    #[OA\Response(response: 403, description: 'No autorizado')]
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
    #[OA\Delete(path: '/api/recipes/{id}', summary: 'Borrar una receta', tags: ['Recetas'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Receta eliminada')]
    #[OA\Response(response: 403, description: 'No autorizado')]
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
