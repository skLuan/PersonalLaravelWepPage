<?php

namespace App\Models\Concerns;

use App\Models\SeoMeta;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/** Optional per-record SEO overrides (seo_meta table). */
trait HasSeo
{
    public static function bootHasSeo(): void
    {
        static::deleting(fn ($model) => $model->seo()->delete());
    }

    public function seo(): MorphOne
    {
        return $this->morphOne(SeoMeta::class, 'seoable');
    }
}
