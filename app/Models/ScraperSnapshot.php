<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScraperSnapshot extends Model
{
    protected $fillable = [
        'snapshot_key',
        'path',
        'payload',
        'stored_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'stored_at' => 'datetime',
    ];
}
