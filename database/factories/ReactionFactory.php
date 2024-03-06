<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ReactionType;
use App\Models\Reaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Reaction>
 */
final class ReactionFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = Reaction::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'staff_id' => \App\Models\Staff::all()->random(),
            'type' => fake()->randomElement(ReactionType::cases()),
        ];
    }
}
