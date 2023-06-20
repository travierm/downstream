<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserMediaPlays>
 */
class UserMediaPlaysFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => fn () => User::factory()->create(),
            'media_id' => function (array $factoryModel) {
                return Media::factory()->create([
                    'user_id' => $factoryModel['user_id']
                ]);
            },
        ];
    }
}
