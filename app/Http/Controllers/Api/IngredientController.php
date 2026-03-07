<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\Recipe;
use OpenApi\Attributes as OA;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(path: '/api/ingredients', summary: 'Listar todos los ingredientes', tags: ['Ingredientes'])]
    #[OA\Response(response: 200, description: 'Lista de ingredientes')]
    public function index()
    {
        return Ingredient::all();

    }

    /**
     * Display the specified resource.
     */
    #[OA\Get(path: '/api/ingredients/{id}', summary: 'Ver un ingrediente y sus recetas', tags: ['Ingredientes'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Ingrediente encontrado')]
    public function show(string $id)
    {
        return $ingredient->load('recipes');

    }

    /**
     * Search the specified resource in storage.
     */
    #[OA\Post(path: '/api/ingredients/search', summary: 'Buscar recetas por ingredientes', tags: ['Ingredientes'])]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
    new OA\Property(property: 'ingredients', type: 'array', items: new OA\Items(type: 'string'), example: ['huevos', 'harina']),
    ]))]
    #[OA\Response(response: 200, description: 'Recetas encontradas')]   
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