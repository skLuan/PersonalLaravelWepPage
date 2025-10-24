<?php

namespace App\Models;

use BinshopsBlog\Models\BinshopsBlogPost;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class BlogPost extends BinshopsBlogPost
{
    /**
     * Override the URL generation method to handle localized routes
     *
     * @return string
     */
    public function url()
    {
        $locale = App::getLocale();
        $availableLocales = config('app.available_locales', ['en']);

        // Check if we're currently on a localized route
        $currentRoute = request()->route();
        if ($currentRoute && str_starts_with($currentRoute->getName(), 'binshopsblog.') && !str_ends_with($currentRoute->getName(), '.fallback')) {
            // We're on a localized route, generate localized URL
            if (in_array($locale, $availableLocales)) {
                try {
                    return route("binshopsblog.single", [
                        'locale' => $locale,
                        'blogPostSlug' => $this->slug
                    ]);
                } catch (\Exception $e) {
                    // Fallback if route generation fails
                }
            }
        }

        // Use fallback route for default locale or when not in localized context
        try {
            return route("binshopsblog.single.fallback", [
                'blogPostSlug' => $this->slug
            ]);
        } catch (\Exception $e) {
            // If fallback route also fails, return a basic URL
            return url('/blog/' . $this->slug);
        }
    }

    /**
     * Override the edit URL method to handle admin routes
     *
     * @return string
     */
    public function edit_url()
    {
        return route("binshopsblog.admin.edit_post", $this->id);
    }
}
