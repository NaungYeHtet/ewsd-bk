<?php

namespace App\Data;

use App\Enums\ReactionType;
use App\Models\Academic;
use App\Models\Idea;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelData\Attributes\Validation\Mimes;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Lazy;

/** @typescript */
class IdeaData extends Data
{
    public function __construct(
        public string $slug,
        #[Rule(['required', 'string', 'min:5', 'max:255'])]
        public string $title,
        #[Rule(['required', 'string', 'min:5', 'max:500'])]
        public string $content,
        #[Mimes('jpg', 'png', 'jpeg', 'pdf', 'doc', 'docx')]
        public ?FileType $file,
        public bool $isOwner,
        public Lazy|StaffData $staff,
        public array $reactionsCount,
        public Lazy|int $viewsCount,
        public int $commentsCount,
        public ?ReactionType $currentReaction,
        public Lazy|CategoryData $category,
        public string $submittedAt,
        public Lazy|string $type,
        public Lazy|Academic $academic,
    ) {
    }

    public static function fromModel(Idea $idea): self
    {
        // dd($idea->staff);
        $file = null;
        if ((bool) $idea->file) {
            $url = config('filesystems.default') == 's3' ? Storage::temporaryUrl($idea->file, now()->addMinutes(3)) : url('/').Storage::url($idea->file);
            $file = new FileType($url, File::extension($idea->file));
        }

        return new self(
            $idea->slug,
            $idea->title,
            $idea->content,
            $file,
            auth()->id() === $idea->staff->id,
            Lazy::create(fn () => $idea->is_anonymous ? null : StaffData::from($idea->staff)->only('id', 'name', 'avatar')),
            $idea->reactions_count,
            Lazy::create(fn () => $idea->views()->count()),
            $idea->comments_count,
            $idea->current_reaction,
            Lazy::create(fn () => CategoryData::from($idea->categories()->first())),
            $idea->created_at->shortAbsoluteDiffForHumans(),
            $idea->getMorphClass(),
            Lazy::create(fn () => AcademicData::from($idea->academic)->only('name'))
        );
    }
}
