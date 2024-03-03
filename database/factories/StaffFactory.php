<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

/**
 * @extends Factory<\App\Models\Staff>
 */
final class StaffFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Staff::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'department_id' => \App\Models\Department::all()->random(),
            'username' => fake()->userName,
            'name' => fake()->name,
            'email' => fake()->safeEmail,
            'email_verified_at' => fake()->optional()->dateTime(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Staff $staff) {
            $role = Role::inRandomOrder()->first();

            $staff->assignRole($role->name);
        });
    }
}
