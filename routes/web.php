<?php

use App\Http\Controllers\BuyController;
use App\Http\Controllers\CatController;
use App\Http\Controllers\GoodController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [CatController::class, 'front'])->name('home');

Route::get('/robots.txt', function () {
    return response()
        ->view('robots')
        ->header('Content-Type', 'text/plain; charset=utf-8');
})->name('robots');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/privacy-policy.html', function () {
    return view('pp');
})->name('pp');

Route::get('/goods/{good:slug}.html', [GoodController::class, 'index'])->name('good');

Route::put('/buy/{good:sku}', [BuyController::class, 'go'])->name('buy');
Route::put('/banner/{id}', [BuyController::class, 'banner'])->whereNumber('id')->name('banner');

Route::get('/imgs/{good:sku}/{index}.jpg', [ImageController::class, 'good'])->whereNumber(['good', 'index'])->name('img');
Route::get('/imgs/small/{good:sku}/{index}.jpg', [ImageController::class, 'good'])->whereNumber(['good', 'index'])->name('img.small');

Route::get('/{cat:slug}.html', [CatController::class, 'index'])->name('cat');

Route::get('{any?}', function () {
    return pleerRedirect();
})->where('any', '.*');
