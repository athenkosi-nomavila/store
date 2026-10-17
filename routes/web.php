<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
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

// Admin area, separate from the storefront.
Route::prefix('admin')->name('admin.')->group(function () {
    // Login, for visitors who are not logged in. Posting is rate limited
    // per IP on top of the per-email login limit.
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'create'])->name('login');
        Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:10,1');
    });

    // Everything else needs a logged-in, approved admin.
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product:id}/edit', [AdminProductController::class, 'edit'])->whereNumber('product')->name('products.edit');
        Route::put('/products/{product:id}', [AdminProductController::class, 'update'])->whereNumber('product')->name('products.update');
        Route::patch('/products/{product:id}/visibility', [AdminProductController::class, 'updateVisibility'])->whereNumber('product')->name('products.visibility');
        Route::get('/products/{product:id}/delete', [AdminProductController::class, 'confirmDelete'])->whereNumber('product')->name('products.delete');
        Route::delete('/products/{product:id}', [AdminProductController::class, 'destroy'])->whereNumber('product')->name('products.destroy');
    });
});
