<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;

class Recipe extends Model
{
     use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'ingredients',
        'steps',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function ingredientes(){
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')
                    ->withPivot('quantity', 'unit')
                    ->withTimestamps();
    }

    public function steps()
    {
        return $this->hasMany(RecipeStep::class)->orderBy('step_number');
    }
}

