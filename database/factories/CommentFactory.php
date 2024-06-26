<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Comment;
use App\Notifications\CommentSubmitted;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Comment>
 */
final class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'staff_id' => \App\Models\Staff::all()->random(),
            'content' => fake()->text,
            'is_anonymous' => fake()->boolean,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Comment $comment) {
            if (rand(0, 3) == 3) {
                \App\Models\Report::factory()->count(rand(0, 5))->create([
                    'reportable_id' => $comment->id,
                    'reportable_type' => $comment->getMorphClass(),
                    'created_at' => fake()->dateTimeBetween($comment->created_at, $comment->created_at->addMonth()),
                ]);
            }

            \App\Models\Reaction::factory()->count(rand(0, 20))->create([
                'reactionable_id' => $comment->id,
                'reactionable_type' => $comment->getMorphClass(),
                'created_at' => fake()->dateTimeBetween($comment->created_at, $comment->created_at->addMonth()),
            ]);

            $comment->commentable->staff->notifications()->create([
                'id' => Str::uuid(),
                'type' => CommentSubmitted::class,
                'data' => CommentSubmitted::getData($comment),
                'created_at' => $comment->created_at,
            ]);
        });
    }
}
