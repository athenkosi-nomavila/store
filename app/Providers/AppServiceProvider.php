<?php

namespace App\Providers;

use App\Catalog\Catalog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(Catalog::class);
    }

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.store');

        // The header menu and listing filters list every category. Error
        // pages use this layout too, so if the database is down the menu
        // drops its category links rather than breaking the error page.
        View::composer(['layouts.app', 'shop.search'], function ($view) {
            $view->with('navCategories', rescue(
                fn () => $this->app->make(Catalog::class)->categories(),
                new Collection,
                report: false,
            ));
        });
    }
}
