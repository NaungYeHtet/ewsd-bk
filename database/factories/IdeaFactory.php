<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Idea;
use App\Notifications\IdeaSubmitted;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Idea>
 */
final class IdeaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Idea::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'staff_id' => \App\Models\Staff::all()->random(),
            'academic_uuid' => \App\Models\Academic::all()->random(),
            'department_id' => \App\Models\Department::all()->random(),
            'title' => fake()->sentence,
            'content' => fake()->text,
            'is_anonymous' => fake()->boolean,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Idea $idea) {
            $academic = $idea->academic;
            $department = $idea->staff->department;

            $closureDate = $academic->closure_date > now() ? now() : $academic->closure_date;
            $randDate = fake()->dateTimeBetween($academic->start_date, $closureDate);
            $idea->created_at = $randDate;
            $idea->updated_at = $randDate;
            $idea->department_id = $department->id;

            $categories = \App\Models\Category::inRandomOrder()->first();

            $idea->categories()->attach($categories);

            $finalClosureDate = $academic->final_closure_date > now() ? now() : $academic->final_closure_date;

            \App\Models\Reaction::factory()->count(rand(0, 20))->create([
                'reactionable_id' => $idea->id,
                'reactionable_type' => $idea->getMorphClass(),
                'created_at' => fake()->dateTimeBetween($academic->start_date, $finalClosureDate),
            ]);

            $idea->save();

            \App\Models\Comment::factory()->count(rand(0, 10))->create([
                'commentable_id' => $idea->id,
                'commentable_type' => $idea->getMorphClass(),
                'created_at' => fake()->dateTimeBetween($academic->start_date, $finalClosureDate),
            ]);

            \App\Models\View::factory()->count(rand(0, 10))->create([
                'viewable_id' => $idea->id,
                'viewable_type' => $idea->getMorphClass(),
                'created_at' => fake()->dateTimeBetween($academic->start_date, $finalClosureDate),
            ]);

            if (rand(0, 3) == 3) {
                \App\Models\Report::factory()->count(rand(0, 5))->create([
                    'reportable_id' => $idea->id,
                    'reportable_type' => $idea->getMorphClass(),
                    'created_at' => fake()->dateTimeBetween($idea->created_at, $finalClosureDate),
                ]);
            }

            $coordinators = $department->staffs()->whereHas('roles', function (Builder $query) {
                $query->where('roles.name', 'QA Coordinator');
            })->get();

            foreach ($coordinators as $coordinator) {
                $coordinator->notifications()->create([
                    'id' => Str::uuid(),
                    'type' => IdeaSubmitted::class,
                    'data' => IdeaSubmitted::getData($idea),
                    'created_at' => $idea->created_at,
                ]);
            }
        });
    }
}
