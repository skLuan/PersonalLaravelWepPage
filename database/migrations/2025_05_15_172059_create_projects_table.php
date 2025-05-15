<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id(); // Clave primaria autoincremental
            $table->string('title'); // Título del proyecto
            $table->string('slug')->unique(); // Slug único para URLs amigables
            $table->text('short_description'); // Breve descripción para la tarjeta
            $table->string('image_path')->nullable(); // URL de la imagen de la tarjeta
            $table->string('site_url')->nullable(); // URL del sitio (opcional)
            $table->text('body'); // Contenido completo del proyecto (HTML/Markdown)
            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
