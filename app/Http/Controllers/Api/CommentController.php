<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(path: '/api/recipes/{id}/comments', summary: 'Ver comentarios de una receta', tags: ['Comentarios'])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Lista de comentarios')]
    public function index(Recipe $recipe)
    {
        return $recipe->comments()->with('user')->get();

    }

    /**
     * Store a newly created resource in storage.
     */
    #[OA\Post(path: '/api/recipes/{id}/comments', summary: 'Crear un comentario', tags: ['Comentarios'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\RequestBody(required: true, content: new OA\JsonContent(properties: [
    new OA\Property(property: 'content', type: 'string', example: 'Qué receta tan buena!'),
    ]))]
    #[OA\Response(response: 201, description: 'Comentario creado')]
    public function store(Request $request, Recipe $recipe)
    {
        $data = $request->validate([
            'content' => 'required|string|max:280',
        ]);

         $comment = $recipe->comments()->create([
            'user_id' => Auth::id(),
            'content' => $data['content'],
        ]);

        return response()->json($comment->load('user'), 201);

    }

    /**
     * Remove the specified resource from storage.
     */
    #[OA\Delete(path: '/api/recipes/{recipe}/comments/{comment}', summary: 'Borrar un comentario', tags: ['Comentarios'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'recipe', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'comment', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Comentario eliminado')]
    #[OA\Response(response: 403, description: 'No autorizado')]
    public function destroy(Recipe $recipe, Comment $comment)
    {
         if ($comment->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $comment->delete();
        return response()->json(['message' => 'Comentario eliminado correctamente'], 200);
    }
}
