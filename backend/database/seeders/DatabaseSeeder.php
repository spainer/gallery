<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Album;
use App\Models\Image;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // create users
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
         $users = collect([$userMM, $userJD]);

         // create tags
         $parentTags = Tag::factory()
             ->count(10)
             ->create();
         $childTags = Tag::factory()
             ->count(25)
             ->sequence(fn (Sequence $sequence) => ['parent' => $parentTags->random()])
             ->create();
         $tags = $parentTags->merge($childTags);

         // create albums
        $albums = Album::factory(10)->create();

        // create images
        for ($i = 0; $i < 100; $i++) {
            Image::factory()
                ->for($albums->random())
                ->for($users->random())
                ->hasAttached($tags->random(rand(0, 5)))
                ->hasExifData(rand(0, 5))
                ->create();
        }
    }
}
