<?php

namespace App\Providers;

use App\Catalog\Catalog;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Catalog::class, fn ($app) => new Catalog($app['config']->get('catalog', [])));
    }

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.store');

        // The header menu and listing filters list every category.
        View::composer(['layouts.app', 'shop.search'], function ($view) {
            $view->with('navCategories', $this->app->make(Catalog::class)->categories());
        });
    }
}
