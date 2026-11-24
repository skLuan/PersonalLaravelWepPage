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
use Illuminate\Support\Facades\App;
use App\Http\Controllers\HomeController;

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
    Route::get('/home', [HomeController::class, 'index'])->name('home');
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

    // Blog routes (BinshopsBlog package) - manually defined to work with locale
    Route::group(['prefix' => 'blog'], function () {
        Route::get('/', 'BinshopsBlog\Controllers\BinshopsBlogReaderController@index')
            ->name('binshopsblog.index');

        Route::get('/search', 'BinshopsBlog\Controllers\BinshopsBlogReaderController@search')
            ->name('binshopsblog.search');

        Route::get('/feed', 'BinshopsBlog\Controllers\BinshopsBlogRssFeedController@feed')
            ->name('binshopsblog.feed');

        Route::get('/category{subcategories}', 'BinshopsBlog\Controllers\BinshopsBlogReaderController@view_category')
            ->where('subcategories', '^[a-zA-Z0-9-_\/]+$')
            ->name('binshopsblog.view_category');

        Route::get('/{blogPostSlug}', 'BinshopsBlog\Controllers\BinshopsBlogReaderController@viewSinglePost')
            ->name('binshopsblog.single');

        // Comments
        Route::group(['middleware' => 'throttle:10,3'], function () {
            Route::post('/save_comment/{blogPostSlug}', 'BinshopsBlog\Controllers\BinshopsBlogCommentWriterController@addNewComment')
                ->name('binshopsblog.comments.add_new_comment');
        });
    });

    // Other routes that need localization
    Route::get('/privacy-policy', function () {
        return view('privacy-policy');
    })->name('privacy-policy');

});

// Fallback routes for default locale (English)
Route::middleware(['setlocale'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home.fallback');
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

    // Blog routes (BinshopsBlog package) - manually defined for fallback
    Route::group(['prefix' => 'blog'], function () {
        Route::get('/', 'BinshopsBlog\Controllers\BinshopsBlogReaderController@index')
            ->name('binshopsblog.index.fallback');

        Route::get('/search', 'BinshopsBlog\Controllers\BinshopsBlogReaderController@search')
            ->name('binshopsblog.search.fallback');

        Route::get('/feed', 'BinshopsBlog\Controllers\BinshopsBlogRssFeedController@feed')
            ->name('binshopsblog.feed.fallback');

        Route::get('/category{subcategories}', 'BinshopsBlog\Controllers\BinshopsBlogReaderController@view_category')
            ->where('subcategories', '^[a-zA-Z0-9-_\/]+$')
            ->name('binshopsblog.view_category.fallback');

        Route::get('/{blogPostSlug}', 'BinshopsBlog\Controllers\BinshopsBlogReaderController@viewSinglePost')
            ->name('binshopsblog.single.fallback');

        // Comments
        Route::group(['middleware' => 'throttle:10,3'], function () {
            Route::post('/save_comment/{blogPostSlug}', 'BinshopsBlog\Controllers\BinshopsBlogCommentWriterController@addNewComment')
                ->name('binshopsblog.comments.add_new_comment.fallback');
        });
    });

    Route::get('/privacy-policy', function () {
        return view('privacy-policy');
    })->name('privacy-policy.fallback');

    // Blog admin routes (BinshopsBlog package) - manually defined for admin functionality
    Route::group(['prefix' => 'blog_admin'], function () {
        Route::get('/', 'BinshopsBlog\Controllers\BinshopsBlogAdminController@index')
            ->name('binshopsblog.admin.index');

        Route::get('/add_post', 'BinshopsBlog\Controllers\BinshopsBlogAdminController@create_post')
            ->name('binshopsblog.admin.create_post');

        Route::post('/add_post', 'BinshopsBlog\Controllers\BinshopsBlogAdminController@store_post')
            ->name('binshopsblog.admin.store_post');

        Route::get('/edit_post/{blogPostId}', 'BinshopsBlog\Controllers\BinshopsBlogAdminController@edit_post')
            ->name('binshopsblog.admin.edit_post');

        Route::patch('/edit_post/{blogPostId}', 'BinshopsBlog\Controllers\BinshopsBlogAdminController@update_post')
            ->name('binshopsblog.admin.update_post');

        Route::get('/remove_photo/{slug}', 'BinshopsBlog\Controllers\BinshopsBlogAdminController@remove_photo')
            ->name('binshopsblog.admin.remove_photo');

        Route::get('/search', 'BinshopsBlog\Controllers\BinshopsBlogAdminController@searchBlog')
            ->name('binshopsblog.admin.searchblog');

        Route::group(['prefix' => "image_uploads"], function () {
            Route::get("/", "BinshopsBlog\Controllers\BinshopsBlogImageUploadController@index")
                ->name("binshopsblog.admin.images.all");

            Route::get("/upload", "BinshopsBlog\Controllers\BinshopsBlogImageUploadController@create")
                ->name("binshopsblog.admin.images.upload");

            Route::post("/upload", "BinshopsBlog\Controllers\BinshopsBlogImageUploadController@store")
                ->name("binshopsblog.admin.images.store");
        });

        Route::delete('/delete_post/{blogPostId}', 'BinshopsBlog\Controllers\BinshopsBlogAdminController@destroy_post')
            ->name('binshopsblog.admin.destroy_post');

        Route::group(['prefix' => 'comments'], function () {
            Route::get('/', 'BinshopsBlog\Controllers\BinshopsBlogCommentsAdminController@index')
                ->name('binshopsblog.admin.comments.index');

            Route::patch('/{commentId}', 'BinshopsBlog\Controllers\BinshopsBlogCommentsAdminController@approve')
                ->name('binshopsblog.admin.comments.approve');

            Route::delete('/{commentId}', 'BinshopsBlog\Controllers\BinshopsBlogCommentsAdminController@destroy')
                ->name('binshopsblog.admin.comments.delete');
        });

        Route::group(['prefix' => 'categories'], function () {
            Route::get('/', 'BinshopsBlog\Controllers\BinshopsBlogCategoryAdminController@index')
                ->name('binshopsblog.admin.categories.index');

            Route::get('/add_category', 'BinshopsBlog\Controllers\BinshopsBlogCategoryAdminController@create_category')
                ->name('binshopsblog.admin.categories.create_category');

            Route::post('/add_category', 'BinshopsBlog\Controllers\BinshopsBlogCategoryAdminController@store_category')
                ->name('binshopsblog.admin.categories.store_category');

            Route::get('/edit_category/{categoryId}', 'BinshopsBlog\Controllers\BinshopsBlogCategoryAdminController@edit_category')
                ->name('binshopsblog.admin.categories.edit_category');

            Route::patch('/edit_category/{categoryId}', 'BinshopsBlog\Controllers\BinshopsBlogCategoryAdminController@update_category')
                ->name('binshopsblog.admin.categories.update_category');

            Route::delete('/delete_category/{categoryId}', 'BinshopsBlog\Controllers\BinshopsBlogCategoryAdminController@destroy_category')
                ->name('binshopsblog.admin.categories.destroy_category');
        });
    });
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


// Route::group(['middleware' => ['web'], 'namespace' => '\BinshopsBlog\Controllers'], function () {

//     /** The main public facing blog routes - show all posts, view a category, rss feed, view a single post, also the add comment route */
//     Route::group(['prefix' => config('binshopsblog.blog_prefix', 'blog')], function () {

//         Route::get('/', 'BinshopsBlogReaderController@index')
//             ->name('binshopsblog.index');

//         Route::get('/search', 'BinshopsBlogReaderController@search')
//             ->name('binshopsblog.search');

//         Route::get('/feed', 'BinshopsBlogRssFeedController@feed')
//             ->name('binshopsblog.feed'); //RSS feed

//         Route::get('/category{subcategories}', 'BinshopsBlogReaderController@view_category')->where('subcategories', '^[a-zA-Z0-9-_\/]+$')->name('binshopsblog.view_category');

//         //        Route::get('/category/{categorySlug}',
//         //            'BinshopsBlogReaderController@view_category')
//         //            ->name('binshopsblog.view_category');

//         Route::get(
//             '/{blogPostSlug}',
//             'BinshopsBlogReaderController@viewSinglePost'
//         )
//             ->name('binshopsblog.single');

//         // throttle to a max of 10 attempts in 3 minutes:
//         Route::group(['middleware' => 'throttle:10,3'], function () {

//             Route::post(
//                 'save_comment/{blogPostSlug}',
//                 'BinshopsBlogCommentWriterController@addNewComment'
//             )
//                 ->name('binshopsblog.comments.add_new_comment');
//         });
//     });

//     /* Admin backend routes - CRUD for posts, categories, and approving/deleting submitted comments */
//     Route::group(['prefix' => config('binshopsblog.admin_prefix', 'blog_admin')], function () {

//         Route::get(
//             '/search',
//             'BinshopsBlogAdminController@searchBlog'
//         )
//             ->name('binshopsblog.admin.searchblog');

//         Route::get('/', 'BinshopsBlogAdminController@index')
//             ->name('binshopsblog.admin.index');

//         Route::get(
//             '/add_post',
//             'BinshopsBlogAdminController@create_post'
//         )
//             ->name('binshopsblog.admin.create_post');


//         Route::post(
//             '/add_post',
//             'BinshopsBlogAdminController@store_post'
//         )
//             ->name('binshopsblog.admin.store_post');


//         Route::get(
//             '/edit_post/{blogPostId}',
//             'BinshopsBlogAdminController@edit_post'
//         )
//             ->name('binshopsblog.admin.edit_post');

//         Route::patch(
//             '/edit_post/{blogPostId}',
//             'BinshopsBlogAdminController@update_post'
//         )
//             ->name('binshopsblog.admin.update_post');

//         //Removes post's photo
//         Route::get(
//             '/remove_photo/{slug}',
//             'BinshopsBlogAdminController@remove_photo'
//         )
//             ->name('binshopsblog.admin.remove_photo');

//         Route::group(['prefix' => "image_uploads",], function () {

//             Route::get("/", "BinshopsBlogImageUploadController@index")->name("binshopsblog.admin.images.all");

//             Route::get("/upload", "BinshopsBlogImageUploadController@create")->name("binshopsblog.admin.images.upload");
//             Route::post("/upload", "BinshopsBlogImageUploadController@store")->name("binshopsblog.admin.images.store");
//         });

//         Route::delete(
//             '/delete_post/{blogPostId}',
//             'BinshopsBlogAdminController@destroy_post'
//         )
//             ->name('binshopsblog.admin.destroy_post');

//         Route::group(['prefix' => 'comments',], function () {

//             Route::get(
//                 '/',
//                 'BinshopsBlogCommentsAdminController@index'
//             )
//                 ->name('binshopsblog.admin.comments.index');

//             Route::patch(
//                 '/{commentId}',
//                 'BinshopsBlogCommentsAdminController@approve'
//             )
//                 ->name('binshopsblog.admin.comments.approve');
//             Route::delete(
//                 '/{commentId}',
//                 'BinshopsBlogCommentsAdminController@destroy'
//             )
//                 ->name('binshopsblog.admin.comments.delete');
//         });

//         Route::group(['prefix' => 'categories'], function () {

//             Route::get(
//                 '/',
//                 'BinshopsBlogCategoryAdminController@index'
//             )
//                 ->name('binshopsblog.admin.categories.index');

//             Route::get(
//                 '/add_category',
//                 'BinshopsBlogCategoryAdminController@create_category'
//             )
//                 ->name('binshopsblog.admin.categories.create_category');
//             Route::post(
//                 '/add_category',
//                 'BinshopsBlogCategoryAdminController@store_category'
//             )
//                 ->name('binshopsblog.admin.categories.store_category');

//             Route::get(
//                 '/edit_category/{categoryId}',
//                 'BinshopsBlogCategoryAdminController@edit_category'
//             )
//                 ->name('binshopsblog.admin.categories.edit_category');

//             Route::patch(
//                 '/edit_category/{categoryId}',
//                 'BinshopsBlogCategoryAdminController@update_category'
//             )
//                 ->name('binshopsblog.admin.categories.update_category');

//             Route::delete(
//                 '/delete_category/{categoryId}',
//                 'BinshopsBlogCategoryAdminController@destroy_category'
//             )
//                 ->name('binshopsblog.admin.categories.destroy_category');
//         });
//     });
// });
