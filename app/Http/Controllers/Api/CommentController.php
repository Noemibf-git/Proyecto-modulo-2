<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Recipe $recipe)
    {
        return $recipe->comments()->with('user')->get();

    }

    /**
     * Store a newly created resource in storage.
     */
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
    public function destroy(Recipe $recipe, Comment $comment)
    {
         if ($comment->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $comment->delete();
        return response()->json(['message' => 'Comentario eliminado correctamente'], 200);
    }
}
