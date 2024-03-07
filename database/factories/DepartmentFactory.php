<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Department>
 */
final class DepartmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Department::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Department $department) {
            $coordinator = \App\Models\Staff::factory()->create([
                'department_id' => $department->id,
            ]);

            $coordinator->assignRole('QA Coordinator');
        });
    }
}
