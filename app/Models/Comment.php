<?php

namespace App\Models;

use App\Enums\ReactionType;
use App\Traits\HasUuids;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['content', 'staff_id', 'is_anonymous'];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(Reaction::class, 'reactionable');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
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
}
