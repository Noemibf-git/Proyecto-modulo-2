<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Recipe;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       $users = User::factory()->count(10)->create();
       $users -> each(function ($users){
        Recipe::factory()->create([
            'user_id' => $users->id
        ]);
       });
    }
}
