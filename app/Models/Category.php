<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'translation_group',
        'locale',
        'slug',
        'name',
        'description',
        'legacy_id',
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    public function url(): string
    {
        return route('blog.category', ['locale' => $this->locale, 'category' => $this->slug]);
    }
}
