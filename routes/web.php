<?php

use App\Http\Controllers\Shop\CatalogController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\NewsletterController;
use App\Http\Controllers\Shop\ProductController;
use Illuminate\Support\Facades\Route;

// Public storefront pages, all read-only GETs.
Route::get('/', HomeController::class)->name('home');
Route::get('/shop', [CatalogController::class, 'index'])->name('shop.index');
Route::get('/search', [CatalogController::class, 'search'])->name('shop.search');
Route::get('/category/{category}', [CatalogController::class, 'category'])->name('shop.category');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::view('/about', 'shop.about')->name('about');
Route::view('/contact', 'shop.contact')->name('contact');

// The only storefront form that posts: CSRF-protected by the web middleware
// group and rate limited to stop scripted abuse.
Route::post('/newsletter', [NewsletterController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('newsletter.subscribe');
