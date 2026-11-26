<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogFeedController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProfileController;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Public pages live under /{locale}. Unprefixed URLs either render with the
| visitor's preferred locale (home, portfolio, privacy) or redirect to the
| localized URL (blog). Admin is the Filament panel at /admin.
*/

require __DIR__ . '/auth.php';

Route::get('/inWork', function () {
    return view('inWork');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::redirect('/privacy', '/privacy-policy');
Route::redirect('/privacypolicy', '/privacy-policy');
Route::redirect('/privacypol', '/privacy-policy');
Route::redirect('/privacypolcy', '/privacy-policy');
Route::redirect('/privac', '/privacy-policy');
Route::redirect('/policy', '/privacy-policy');
Route::redirect('/pp', '/privacy-policy');

// Old admin URLs → Filament panel.
Route::permanentRedirect('/projects', '/admin/projects');
Route::get('/projects/{any}', fn () => redirect('/admin/projects', 301))->where('any', '.*');
Route::permanentRedirect('/blog_admin', '/admin/posts');
Route::get('/blog_admin/{any}', fn () => redirect('/admin/posts', 301))->where('any', '.*');

$availableLocales = config('app.available_locales', ['en']);

Route::prefix('{locale}')
    ->where(['locale' => implode('|', $availableLocales)])
    ->middleware(['setlocale'])
    ->group(function () {
        Route::get('/', function () {
            return view('home');
        })->name('home');

        Route::get('/home', fn () => redirect()->route('home'));

        Route::resource('portfolio', PortfolioController::class)
            ->only('index')
            ->names(['index' => 'portfolio']);

        Route::prefix('blog')->name('blog.')->controller(BlogController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/search', 'search')->name('search');
            Route::get('/feed', BlogFeedController::class)->name('feed');
            Route::get('/category/{category}', 'category')->name('category');
            Route::get('/{post}', 'show')->name('show');
        });

        Route::get('/privacy-policy', function () {
            return view('privacy-policy');
        })->name('privacy-policy');
    });

Route::middleware(['setlocale'])->group(function () {
    Route::get('/', function () {
        return view('home');
    })->name('home.fallback');

    Route::redirect('/home', '/');

    Route::resource('portfolio', PortfolioController::class)
        ->only('index')
        ->names(['index' => 'portfolio.fallback']);

    // Unprefixed blog URLs (old binshops links). Posts and categories go to their
    // own language (301); list pages go to the visitor's language (302).
    Route::prefix('blog')->group(function () {
        Route::get('/', fn () => redirect()->route('blog.index'))->name('blog.index.fallback');
        Route::get('/search', fn (Request $request) => redirect()->route('blog.search', $request->query()))
            ->name('blog.search.fallback');
        Route::get('/feed', fn () => redirect()->route('blog.feed'))->name('blog.feed.fallback');
        Route::get('/category/{slug}', function (string $slug) {
            $category = Category::where('slug', $slug)->orderByRaw('locale = ? desc', [app()->getLocale()])->firstOrFail();

            return redirect($category->url(), 301);
        })->name('blog.category.fallback');
        Route::get('/{slug}', function (string $slug) {
            $post = Post::published()->where('slug', $slug)->orderByRaw('locale = ? desc', [app()->getLocale()])->firstOrFail();

            return redirect($post->url(), 301);
        })->name('blog.show.fallback');
    });

    Route::get('/privacy-policy', function () {
        return view('privacy-policy');
    })->name('privacy-policy.fallback');
});

// Language switcher
Route::get('/locale/{locale}', function ($locale) {
    $availableLocales = config('app.available_locales', ['en']);
    if (!in_array($locale, $availableLocales)) {
        abort(404);
    }
    session(['locale' => $locale]);

    $parsedUrl = parse_url(url()->previous());
    $path = $parsedUrl['path'] ?? '/';
    $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';

    // Strip only a whole locale segment: "/essay" must not lose "es".
    $localePattern = '#^/(' . implode('|', array_map('preg_quote', $availableLocales)) . ')(?=/|$)#';
    $path = preg_replace($localePattern, '', $path) ?: '/';

    $newPath = rtrim('/' . $locale . $path, '/');

    // Pages without a localized route (/projects, /login…) stay where they are.
    try {
        Route::getRoutes()->match(Request::create($newPath));
    } catch (HttpException) {
        return redirect($path . $query);
    }

    return redirect($newPath . $query);
})->name('locale.switch')->where(['locale' => implode('|', $availableLocales)]);
