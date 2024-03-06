<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Events\IdeaSubmitted;
use App\Models\Idea;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'title' => fake()->sentence,
            'content' => fake()->text,
            'is_anonymous' => fake()->boolean,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Idea $idea) {
            $categories = \App\Models\Category::inRandomOrder()->limit(rand(1, 3))->get();

            $idea->categories()->attach($categories);

            \App\Models\Reaction::factory()->count(rand(0, 20))->create([
                'reactionable_id' => $idea->id,
                'reactionable_type' => $idea->getMorphClass(),
            ]);

            \App\Models\Comment::factory()->count(rand(0, 10))->create([
                'commentable_id' => $idea->id,
                'commentable_type' => $idea->getMorphClass(),
            ]);

            IdeaSubmitted::dispatch($idea);
        });
    }
}
