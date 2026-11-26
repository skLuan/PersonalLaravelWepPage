<?php

namespace App\Models;

use App\Models\Concerns\HasSeo;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory, HasSeo, HasTranslations;

    protected $fillable = [
        'translation_group',
        'locale',
        'slug',
        'title',
        'subtitle',
        'excerpt',
        'body',
        'cover_image',
        'cover_alt',
        'author_id',
        'published_at',
        'legacy_id',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /** Published and not scheduled for the future. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->lte(now());
    }

    public function url(): string
    {
        return route('blog.show', ['locale' => $this->locale, 'post' => $this->slug]);
    }

    public function coverUrl(): ?string
    {
        return $this->cover_image ? Storage::disk('blog')->url($this->cover_image) : null;
    }

    /** Plain-text summary: the excerpt, or the start of the body. */
    public function summary(int $limit = 200): string
    {
        $text = $this->excerpt ?: strip_tags((string) $this->body);

        return Str::limit(trim(preg_replace('/\s+/', ' ', html_entity_decode($text))), $limit);
    }
}
