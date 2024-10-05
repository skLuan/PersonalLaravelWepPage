<?php

use App\Http\Controllers\CurriculumVitae;
use App\Http\Controllers\ProfileController;
use App\Providers\NotionService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect('inWork');
});
Route::get('/blog', function() {
    return redirect('https://erazoluan.notion.site/Blog-060c7617bd494cbbbd7badfff7afbe35');
})->name('blog');
//Route::get('/blog/inWork', [CurriculumVitae::class, 'showBlog']);
Route::get('/inWork', function () {
    return view('inWork');
});
Route::get('/portfolio', function () {
    return redirect('https://erazoluan.notion.site/Professional-Life-113cff97a3d780a59e0fdb59a1263a1e');
})->name('portfolio');




Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
