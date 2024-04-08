<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Academic extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'start_date', 'closure_date', 'final_closure_date'];

    protected $primaryKey = 'uuid';

    protected $casts = [
        'start_date' => 'date',
        'closure_date' => 'date',
        'final_closure_date' => 'date',
    ];

    public function ideas(): HasMany
    {
        return $this->hasMany(Idea::class);
    }

    protected function dataFileName(): Attribute
    {
        return Attribute::make(
            get: fn () => "data-{$this->start_date->format('Y-m-d')}-{$this->final_closure_date->format('Y-m-d')}"
        );
    }

    protected function filesFileName(): Attribute
    {
        return Attribute::make(
            get: fn () => "files-{$this->start_date->format('Y-m-d')}-{$this->final_closure_date->format('Y-m-d')}"
        );
    }

    protected function isActiveAcademic(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->start_date <= now() && $this->final_closure_date >= now()
        );
    }

    protected function isPreviousAcademic(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->final_closure_date < now()
        );
    }

    protected function isFutureAcademic(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->start_date > now()
        );
    }

    public function scopeIsActive(Builder $query): void
    {
        $query->where('start_date', '<=', now())->where('final_closure_date', '>=', now());
    }

    public function scopeIsPrevious(Builder $query): void
    {
        $query->where('final_closure_date', '<', now())->orderBy('final_closure_date', 'desc');
    }

    public static function isDateBetweenStartAndClosureDate($date = null): bool
    {
        $date = $date ?? now();

        return Academic::where('start_date', '<=', $date)->where('closure_date', '>=', $date)->exists();
    }

    public static function isDateBetweenStartAndFinalClosureDate($date = null): bool
    {
        $date = $date ?? now();

        return Academic::where('start_date', '<=', $date)->where('final_closure_date', '>=', $date)->exists();
    }
}
