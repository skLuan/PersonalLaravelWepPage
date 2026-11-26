<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

/**
 * Row-per-locale translations: rows that translate each other share
 * `translation_group`. Needs `locale` and `translation_group` columns.
 */
trait HasTranslations
{
    public static function bootHasTranslations(): void
    {
        static::creating(function ($model) {
            $model->translation_group ??= (string) Str::uuid();
            $model->locale ??= app()->getLocale();
        });
    }

    public function scopeLocale(Builder $query, ?string $locale = null): Builder
    {
        return $query->where($this->qualifyColumn('locale'), $locale ?? app()->getLocale());
    }

    /** Other-language versions of this row (excludes itself). */
    public function translations(): Collection
    {
        return static::query()
            ->where('translation_group', $this->translation_group)
            ->whereKeyNot($this->getKey())
            ->get();
    }

    public function translation(string $locale): ?static
    {
        if ($locale === $this->locale) {
            return $this;
        }

        return static::query()
            ->where('translation_group', $this->translation_group)
            ->where('locale', $locale)
            ->first();
    }
}
