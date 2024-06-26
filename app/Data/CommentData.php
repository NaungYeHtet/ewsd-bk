<?php

namespace App\Data;

use App\Enums\ReactionType;
use App\Models\Comment;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Spatie\LaravelData\Optional;

/** @typescript */
class CommentData extends Data
{
    public function __construct(
        #[MapInputName('uuid')]
        public string|Optional $id,
        #[Rule(['required', 'string', 'min:5', 'max:500'])]
        public string $content,
        public bool|Optional $isOwner,
        #[MapInputName('is_anonymous')]
        public bool|Optional $isAnonymous,
        public Lazy|StaffData|Optional $staff,
        public string|Optional $submittedAt,
        public Lazy|string|Optional $type,
        public array|Optional $reactionsCount,
        public null|ReactionType|Optional $currentReaction,
    ) {
    }

    public static function fromModel(Comment $comment): self
    {
        return new self(
            $comment->uuid,
            $comment->content,
            auth()->id() === $comment->staff->id,
            $comment->is_anonymous,
            Lazy::create(fn () => $comment->is_anonymous ? null : StaffData::from($comment->staff)->only('id', 'name', 'avatar')),
            $comment->created_at->shortAbsoluteDiffForHumans(),
            $comment->getMorphClass(),
            $comment->reactions_count,
            $comment->current_reaction,
        );
    }
}
