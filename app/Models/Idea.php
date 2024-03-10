<?php

namespace App\Models;

use App\Enums\ReactionType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Idea extends Model
{
    use HasFactory;
    use HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'file',
        'is_anonymous',
        'reactions_count',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $casts = [
        'reactions_count' => 'array',
        'is_anonymous' => 'boolean',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function categories(): MorphToMany
    {
        return $this->morphToMany(Category::class, 'categoriable');
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactionable');
    }

    protected function currentReaction(): Attribute
    {
        return Attribute::make(
            get: function () {
                $reaction = $this->reactions()->where('staff_id', auth()->id())->first();

                return $reaction ? $reaction->type : null;
            }
        );
    }

    protected function reactionsCount(): Attribute
    {
        return Attribute::make(
            get: function () {
                $counts = [];

                foreach (ReactionType::cases() as $reactionType) {
                    $counts[$reactionType->value] = $this->reactions()->where('type', $reactionType->value)->count();
                }

                return $counts;
            }
        );
    }

    protected function commentsCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->comments()->count()
        );
    }
}
