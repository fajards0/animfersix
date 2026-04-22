<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait GeneratesUniqueSlug
{
    protected function generateUniqueSlug(string $value, string $modelClass, ?Model $ignore = null): string
    {
        $baseSlug = Str::slug($value);

        if (blank($baseSlug)) {
            $baseSlug = Str::lower(Str::random(8));
        }

        $slug = $baseSlug;
        $counter = 2;

        while ($this->slugExists($slug, $modelClass, $ignore)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected function slugExists(string $slug, string $modelClass, ?Model $ignore = null): bool
    {
        $query = $modelClass::query()->where('slug', $slug);

        if ($ignore) {
            $query->whereKeyNot($ignore->getKey());
        }

        return $query->exists();
    }
}
