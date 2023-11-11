<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $userMM = User::factory()->create([
             'id' => 'MM',
             'name' => 'Max Mustermann',
             'email' => 'max@mustermann.de'
         ]);

         $userJD = User::factory()->create([
             'id' => 'JD',
             'name' => 'John Doe',
             'email' => 'john@doe.com'
         ]);
    }
}
