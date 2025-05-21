<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Project extends Model
{
    use HasFactory, HasSlug;

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'image_path',
        'site_url',
        'body',
    ];

    // Relación muchos a muchos con Skill
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'project_skill')
                    ->withTimestamps();
    }

    // Configura las opciones del slug
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title') // Genera el slug a partir del título
            ->saveSlugsTo('slug'); // Guarda el slug en el campo 'slug'
    }

    // Especifica que las rutas deben usar el campo 'slug' en lugar de 'id'
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
