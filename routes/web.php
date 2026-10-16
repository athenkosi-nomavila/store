<?php

use App\Http\Controllers\Shop\CatalogController;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;
use Illuminate\Support\Facades\Route;

// Public storefront pages, all read-only GETs.
Route::get('/', HomeController::class)->name('home');
Route::get('/shop', [CatalogController::class, 'index'])->name('shop.index');
Route::get('/search', [CatalogController::class, 'search'])->name('shop.search');
Route::get('/category/{slug}', [CatalogController::class, 'category'])->name('shop.category');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
