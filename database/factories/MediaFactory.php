<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    use WithFaker;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'origin' => 'youtube#search',
            'type' => 'youtube',
            'index' => Str::random(10),
            'subtype' => 'video',
            'title' => $this->faker->words(3, true),
            'thumbnail' => $this->faker->url(),
        ];
    }
}
