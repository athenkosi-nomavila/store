<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Catch N+1 queries and silently dropped attributes while developing.
        Model::shouldBeStrict(! $this->app->isProduction());

        Paginator::defaultView('vendor.pagination.lumora');

        // The header menu, footer and listing filters all list categories;
        // load them once per request.
        $navCategories = null;
        View::composer(['layouts.app', 'shop.search'], function ($view) use (&$navCategories) {
            $navCategories ??= Category::orderBy('name')->get(['id', 'name', 'slug']);
            $view->with('navCategories', $navCategories);
        });
    }
}
