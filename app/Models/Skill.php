<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    // Campos que se pueden llenar masivamente
    protected $fillable = ['name'];

    // Relación muchos a muchos con Project
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_skill')
                    ->withTimestamps();
    }
}
