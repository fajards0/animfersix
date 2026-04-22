<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Fluent;

class Watchlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'anime_api_id',
        'anime_title',
        'poster_path',
        'anime_url',
        'score',
        'status',
        'type',
        'studio',
        'year',
        'genres',
    ];

    protected $casts = [
        'score' => 'float',
        'year' => 'integer',
        'genres' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function toAnimeCard(): Fluent
    {
        return new Fluent([
            'id' => $this->anime_api_id,
            'route_id' => $this->anime_url,
            'title' => $this->anime_title,
            'poster_image' => $this->poster_path ?: 'https://placehold.co/600x900/09090b/f97316?text=Anime',
            'synopsis' => 'Disimpan dari katalog Otakudesu API.',
            'studio' => $this->studio,
            'year' => $this->year,
            'status' => $this->status,
            'score' => $this->score ?? 0,
            'type' => $this->type ?: 'Anime',
            'genres' => collect($this->genres ?? [])->map(fn ($genre) => new Fluent(['name' => $genre])),
            'episode_label' => null,
        ]);
    }
}
