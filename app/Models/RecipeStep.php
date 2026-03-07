<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeStep extends Model
{
    protected $table = 'recipes_steps';
    protected $fillable = ['recipe_id', 'step_number', 'description'];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
