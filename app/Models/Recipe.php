<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;
use App\Models\Ingredient;
use App\Models\RecipeStep;

class Recipe extends Model
{
     use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'imagen',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function ingredients(){
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')
                    ->withPivot('quantity', 'unit')
                    ->withTimestamps();
    }

    public function steps()
    {
        return $this->hasMany(RecipeStep::class)->orderBy('step_number');
    }
}

