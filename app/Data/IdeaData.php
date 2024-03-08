<?php

namespace App\Data;

use App\Enums\ReactionType;
use App\Models\Idea;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Mimes;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class IdeaData extends Data
{
    public function __construct(
        #[Rule(['required', 'string', 'min:5', 'max:255'])]
        public string $title,
        #[Rule(['required', 'string', 'min:5', 'max:500'])]
        public string $content,
        #[Rule(['required', 'boolean']), MapInputName('is_anonymous')]
        #[Mimes('jpg', 'png', 'jpeg', 'pdf', 'doc', 'docx')]
        public ?UploadedFile $file,
        public Lazy|StaffData $staff,
        public array $reactionsCount,
        public int $commentsCount,
        public ?ReactionType $currentReaction,
    ) {
    }

    public static function fromModel(Idea $idea): self
    {
        // dd($idea->staff);
        return new self(
            $idea->title,
            $idea->content,
            $idea->file,
            Lazy::create(fn () => $idea->is_anonymous ? null : StaffData::from($idea->staff)),
            $idea->reactions_count,
            $idea->comments_count,
            $idea->current_reaction
        );
    }
}
