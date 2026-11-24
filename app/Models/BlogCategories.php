<?php

namespace App\Models;

use Illuminate\Support\Facades\App;
use BinshopsBlog\Models\BinshopsBlogCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategories extends BinshopsBlogCategory
{
    protected $parentColumn = 'parent_id';
    public $siblings = array();

    public $fillable = [
        'category_name',
        'slug',
        'category_description',
        'parent_id'
    ];

    /**
     * Returns the public facing URL of showing blog posts in this category
     * @return string
     */
    public function url()
    {
        $theChainString = "";
        $chain = $this->getAncestorsAndSelf();
        $locale = App::getLocale();
        $availableLocales = config('app.available_locales', ['en']);
        foreach ($chain as $category){
            $theChainString .=  "/" . $category->slug;
        }


        // Check if we're currently on a localized route
        $currentRoute = request()->route();
        if ($currentRoute && str_starts_with($currentRoute->getName(), 'binshopsblog.') && !str_ends_with($currentRoute->getName(), '.fallback')) {
            // We're on a localized route, generate localized URL
            if (in_array($locale, $availableLocales)) {
                try {
                    return route("binshopsblog.view_category", $theChainString);
                } catch (\Exception $e) {
                    // Fallback if route generation fails
                }
            }
        }

        // Use fallback route for default locale or when not in localized context
        try {
            return route("binshopsblog.view_category.fallback",  $theChainString);
        } catch (\Exception $e) {
            // If fallback route also fails, return a basic URL
            return url('/blog/' . $this->slug);
        }
    }

    /**
     * Returns the URL for an admin user to edit this category
     * @return string
     */
    public function edit_url()
    {
        return route("binshopsblog.admin.categories.edit_category", $this->id);
    }

    public function loadSiblings(){
        $this->siblings = $this->children()->get();
    }


}
