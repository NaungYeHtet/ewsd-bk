<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\Staff;
use App\Notifications\ReportSubmitted;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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

            $admins = Staff::whereRelation('roles', 'name', '=', 'Admin')->get();
            foreach ($admins as $admin) {
                $admin->notifications()->create([
                    'id' => Str::uuid(),
                    'type' => ReportSubmitted::class,
                    'data' => ReportSubmitted::getData($report),
                    'created_at' => $report->created_at,
                ]);
            }
        });
    }
}
