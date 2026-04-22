<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Episode extends Model
{
    use HasFactory;

    protected $fillable = [
        'anime_id',
        'title',
        'slug',
        'episode_number',
        'synopsis',
        'video_url',
        'duration_minutes',
        'aired_at',
        'is_published',
    ];

    protected $casts = [
        'aired_at' => 'datetime',
        'duration_minutes' => 'integer',
        'episode_number' => 'integer',
        'is_published' => 'boolean',
    ];

    public function anime(): BelongsTo
    {
        return $this->belongsTo(Anime::class);
    }

    public function getStreamUrlAttribute(): string
    {
        return $this->video_url ?: 'https://samplelib.com/lib/preview/mp4/sample-5s.mp4';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
