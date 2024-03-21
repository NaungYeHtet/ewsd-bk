<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Notification;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'staff_id' => \App\Models\Staff::all()->random(),
            'reason' => fake()->sentence(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Report $report) {
            Notification::send(Staff::whereRelation('roles', 'name', '=', 'Admin')->get(), new \App\Notifications\ReportSubmitted($report));
        });
    }
}
