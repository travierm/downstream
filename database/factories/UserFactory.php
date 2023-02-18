<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'hash' => Str::random(10),
            'display_name' => 'ds_test_user_'.$this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            //'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'api_token' => Str::random(10),
            'remember_token' => Str::random(10),
            'private' => 0,
        ];
    }
}
