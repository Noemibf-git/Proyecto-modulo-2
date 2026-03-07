<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\RecipeStep;
use OpenApi\Attributes as OA;

class RecipeStepController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(path: '/api/recipes/{id}/steps', summary: 'Ver los pasos de una receta', tags: ['Pasos'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Lista de pasos')]
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
