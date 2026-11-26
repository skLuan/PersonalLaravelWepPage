<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Own blog tables (replace binshops_blog_*).
 *
 * One row per language. Rows that are translations of each other share
 * `translation_group`, so each language has its own slug and URL (hreflang).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('translation_group')->index();
            $table->string('locale', 5);
            $table->string('slug');
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('legacy_id')->nullable()->comment('binshops_blog_categories.id');
            $table->timestamps();

            $table->unique(['locale', 'slug']);
            $table->unique(['translation_group', 'locale']);
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->uuid('translation_group')->index();
            $table->string('locale', 5);
            $table->string('slug');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('cover_image')->nullable()->comment('path on the "blog" disk');
            $table->string('cover_alt')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('published_at')->nullable()->index()->comment('null = draft; future = scheduled');
            $table->unsignedInteger('legacy_id')->nullable()->unique()->comment('binshops_blog_posts.id');
            $table->timestamps();

            $table->unique(['locale', 'slug']);
            $table->unique(['translation_group', 'locale']);
        });

        Schema::create('category_post', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->primary(['category_id', 'post_id']);
        });

        // SEO overrides for any page-like model (posts now; projects and static pages later).
        Schema::create('seo_meta', function (Blueprint $table) {
            $table->id();
            $table->morphs('seoable');
            $table->string('title')->nullable();
            $table->string('description', 320)->nullable();
            $table->string('og_image')->nullable();
            $table->string('canonical_url')->nullable();
            $table->boolean('noindex')->default(false);
            $table->string('focus_keyword')->nullable();
            $table->timestamps();

            $table->unique(['seoable_type', 'seoable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_meta');
        Schema::dropIfExists('category_post');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('categories');
    }
};
