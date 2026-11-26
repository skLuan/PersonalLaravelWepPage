<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Public blog, under /{locale}/blog. Lookups are scoped to the URL locale:
 * every language has its own slugs.
 */
class BlogController extends Controller
{
    public const PER_PAGE = 10;

    public function index(): View
    {
        return view('blog.index', [
            'posts' => $this->publishedPosts()->paginate(self::PER_PAGE),
            'category' => null,
        ]);
    }

    public function category(string $slug): View|RedirectResponse
    {
        $category = Category::query()->locale()->where('slug', $slug)->first();

        if (! $category) {
            // A slug from another language: send to this language's version, if any.
            $other = Category::query()->where('slug', $slug)->firstOrFail();

            return redirect(($other->translation(app()->getLocale()) ?? $other)->url());
        }

        return view('blog.index', [
            'posts' => $this->publishedPosts()
                ->whereHas('categories', fn ($query) => $query->whereKey($category->getKey()))
                ->paginate(self::PER_PAGE),
            'category' => $category,
        ]);
    }

    public function search(Request $request): View
    {
        $term = trim((string) $request->query('s', ''));

        $posts = $this->publishedPosts()
            ->when($term !== '', function ($query) use ($term) {
                $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $term).'%';
                $query->where(fn ($q) => $q
                    ->where('title', 'like', $like)
                    ->orWhere('subtitle', 'like', $like)
                    ->orWhere('excerpt', 'like', $like)
                    ->orWhere('body', 'like', $like));
            })
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('blog.search', ['posts' => $posts, 'term' => $term]);
    }

    public function show(Request $request, string $slug): View|RedirectResponse
    {
        $post = Post::query()->locale()->where('slug', $slug)->with(['author', 'categories', 'seo'])->first();

        if (! $post) {
            // Slug from another language (e.g. after the language switcher):
            // go to the translation, or to the post in its own language.
            $other = Post::query()->published()->where('slug', $slug)->firstOrFail();
            $target = $other->translation(app()->getLocale());

            return redirect(($target && $target->isPublished() ? $target : $other)->url());
        }

        // Drafts and scheduled posts are visible to admins only (preview).
        abort_unless($post->isPublished() || $request->user()?->is_admin, 404);

        return view('blog.show', [
            'post' => $post,
            'translations' => $post->translations()->filter->isPublished(),
        ]);
    }

    private function publishedPosts()
    {
        return Post::query()
            ->locale()
            ->published()
            ->with('categories')
            ->latest('published_at');
    }
}
