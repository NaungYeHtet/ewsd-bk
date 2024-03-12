<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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
        // $filepath = storage_path('app/public/images/avatars');
        // if (! File::exists($filepath)) {
        //     File::makeDirectory($filepath, 0777, true);
        // }

        return [
            'department_id' => \App\Models\Department::all()->random(),
            'username' => fake()->userName,
            'name' => fake()->name,
            'email' => fake()->safeEmail,
            // 'avatar' => fake()->optional()->image($filepath, 260, 260, 'animals'),
            'email_verified_at' => fake()->dateTime(),
            // 'email_verified_at' => fake()->optional()->dateTime(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user is suspended.
     */
    public function assignRole(): Factory
    {
        return $this->afterCreating(function (Staff $staff) {
            $staff->assignRole(fake()->randomElement(['Academic Staff', 'Support']));
        });
    }
}
