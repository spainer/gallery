<?php

namespace Database\Factories;

use App\Models\ExifData;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => substr($this->faker->fileExtension(), 0, 4),
            'imageDate' => $this->faker->dateTime()
        ];
    }
}
