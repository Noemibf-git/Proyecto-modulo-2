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
      $this->call(UserSeeder::class);
       $users = User::all();
       $users -> each(function ($users){
        Recipe::factory()->create([
            'user_id' => $users->id
        ]);
       });
    }
}
