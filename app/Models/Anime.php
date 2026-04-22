<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Anime extends Model
{
    use HasFactory;

    public const STATUSES = [
        'ongoing' => 'Ongoing',
        'completed' => 'Completed',
        'upcoming' => 'Upcoming',
    ];

    public const RATINGS = [
        'G' => 'G',
        'PG' => 'PG',
        'PG-13' => 'PG-13',
        'R-17' => 'R-17',
    ];

    protected $fillable = [
        'title',
        'slug',
        'synopsis',
        'poster_path',
        'banner_path',
        'studio',
        'year',
        'status',
        'rating',
        'score',
        'type',
        'views',
        'is_trending',
        'is_popular',
    ];

    protected $casts = [
        'year' => 'integer',
        'score' => 'decimal:1',
        'views' => 'integer',
        'is_trending' => 'boolean',
        'is_popular' => 'boolean',
    ];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class)->withTimestamps();
    }

    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }

    public function watchlists(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $nested) use ($search) {
                    $nested
                        ->where('title', 'like', '%' . $search . '%')
                        ->orWhere('studio', 'like', '%' . $search . '%')
                        ->orWhere('synopsis', 'like', '%' . $search . '%');
                });
            })
            ->when($filters['genre'] ?? null, function (Builder $query, string $genre) {
                $query->whereHas('genres', fn (Builder $genreQuery) => $genreQuery->where('slug', $genre));
            })
            ->when($filters['year'] ?? null, fn (Builder $query, string $year) => $query->where('year', $year))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['rating'] ?? null, fn (Builder $query, string $rating) => $query->where('rating', $rating));
    }

    public function getPosterImageAttribute(): string
    {
        return $this->resolveMediaUrl(
            $this->poster_path,
            'https://placehold.co/600x900/09090b/f97316?text=' . urlencode($this->title)
        );
    }

    public function getBannerImageAttribute(): string
    {
        return $this->resolveMediaUrl(
            $this->banner_path,
            'https://placehold.co/1400x700/0f172a/e11d48?text=' . urlencode($this->title)
        );
    }

    private function resolveMediaUrl(?string $value, string $fallback): string
    {
        if (blank($value)) {
            return $fallback;
        }

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return Storage::disk('public')->url($value);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
