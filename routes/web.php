<?php

use App\Http\Controllers\CurriculumVitae;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Providers\NotionService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

require __DIR__ . '/auth.php';
// Route::get('/blog', function() {
    //     return redirect('https://erazoluan.notion.site/Blog-060c7617bd494cbbbd7badfff7afbe35');
    // })->name('blog');
// Route::get('/blog/inWork', [CurriculumVitae::class, 'showBlog']);
Route::get('/inWork', function () {
    return view('inWork');
});
// Route::get('inwork/portfolio', function () {
//     return view('portfolio');
// })->name('inWorkPortfolio');

// Route::get('/blog', function () {
//     return view('blog');
// })->name('blog');
// Route::get('/portfolio', function () {
//     return redirect('https://erazoluan.notion.site/Professional-Life-113cff97a3d780a59e0fdb59a1263a1e');
// })->name('portfolio');




Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');
Route::redirect('/privacy', '/privacy-policy');
Route::redirect('/privacypolicy', '/privacy-policy');
Route::redirect('/privacypol', '/privacy-policy');
Route::redirect('/privacypolcy', '/privacy-policy');
Route::redirect('/privac', '/privacy-policy');
Route::redirect('/policy', '/privacy-policy');
Route::redirect('/pp', '/privacy-policy');

Auth::routes();


Route::resource('projects', ProjectController::class);

$availableLocales = config('app.available_locales', ['en']);

// Localized routes
Route::prefix('{locale}')
    ->where(['locale' => implode('|', $availableLocales)])
    ->middleware(['setlocale'])
    ->group(function () use ($availableLocales) {

    // Home routes
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/', function () {
        return view('home');
    })->name('home');

    // Portfolio routes
    Route::resource('portfolio', PortfolioController::class)->names([
        'index' => 'portfolio',
        'show' => 'portfolio.show',
        'create' => 'portfolio.create',
        'store' => 'portfolio.store',
        'edit' => 'portfolio.edit',
        'update' => 'portfolio.update',
        'destroy' => 'portfolio.destroy',
    ]);

    // Blog routes (BinshopsBlog package)
    Route::get('/blog', function () {
        return view('vendor.binshopsblog.index');
    })->name('binshopsblog.index');

    // Other routes that need localization
    Route::get('/privacy-policy', function () {
        return view('privacy-policy');
    })->name('privacy-policy');

});

// Fallback routes for default locale (English)
Route::middleware(['setlocale'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home.fallback');
    Route::get('/', function () {
        return view('home');
    })->name('home.fallback');

    Route::resource('portfolio', PortfolioController::class)->names([
        'index' => 'portfolio.fallback',
        'show' => 'portfolio.show.fallback',
        'create' => 'portfolio.create.fallback',
        'store' => 'portfolio.store.fallback',
        'edit' => 'portfolio.edit.fallback',
        'update' => 'portfolio.update.fallback',
        'destroy' => 'portfolio.destroy.fallback',
    ]);

    // Blog routes (BinshopsBlog package)
    Route::get('/blog', function () {
        return view('vendor.binshopsblog.index');
    })->name('binshopsblog.index.fallback');

    Route::get('/privacy-policy', function () {
        return view('privacy-policy');
    })->name('privacy-policy.fallback');
});

// Language switcher route
Route::get('/locale/{locale}', function ($locale) {
    $availableLocales = config('app.available_locales', ['en']);

    if (!in_array($locale, $availableLocales)) {
        abort(404);
    }

    // Store locale in session
    session(['locale' => $locale]);

    // Redirect back to the same page with new locale
    $previousUrl = url()->previous();
    $parsedUrl = parse_url($previousUrl);

    // If URL already has a locale prefix, replace it
    $path = $parsedUrl['path'] ?? '/';
    foreach ($availableLocales as $availableLocale) {
        if (str_starts_with($path, '/' . $availableLocale)) {
            $path = substr($path, strlen('/' . $availableLocale));
            break;
        }
    }

    // Add new locale prefix
    $newPath = '/' . $locale . $path;

    return redirect($newPath);
})->name('locale.switch')->where(['locale' => implode('|', $availableLocales)]);
// Route::get('/blog', function () {
//     return view('vendor.binshopsblog.index');
// })->name('blog');

// Route::get('/', 'BinshopsBlogReaderController@index')
//     ->name('binshopsblog.index');

// Route::redirect('/', 'front-page');