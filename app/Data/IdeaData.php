<?php

namespace App\Data;

use App\Enums\ReactionType;
use App\Models\Idea;
use Illuminate\Support\Facades\File;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Mimes;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

class IdeaData extends Data
{
    public function __construct(
        #[Rule(['required', 'string', 'min:5', 'max:255'])]
        public string $title,
        #[Rule(['required', 'string', 'min:5', 'max:500'])]
        public string $content,
        #[Rule(['required', 'boolean']), MapInputName('is_anonymous')]
        #[Mimes('jpg', 'png', 'jpeg', 'pdf', 'doc', 'docx')]
        public ?FileType $file,
        public Lazy|StaffData $staff,
        public array $reactionsCount,
        public int $viewsCount,
        public int $commentsCount,
        public ?ReactionType $currentReaction,
        public Lazy|CategoryData $category,
    ) {
    }

    public static function fromModel(Idea $idea): self
    {
        // dd($idea->staff);
        $file = null;
        if ((bool) $idea->file) {
            $file = new FileType(asset($idea->file), File::extension($idea->file));
        }

        return new self(
            $idea->title,
            $idea->content,
            $file,
            Lazy::create(fn () => $idea->is_anonymous ? null : StaffData::from($idea->staff)->only('name', 'avatar')),
            $idea->reactions_count,
            $idea->views_count,
            $idea->comments_count,
            $idea->current_reaction,
            Lazy::create(fn () => CategoryData::from($idea->category)),
        );
    }
}
