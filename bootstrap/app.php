<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\SecurityHeaders;
use App\Support\DatabaseOutage;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\ViewErrorBag;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            SecurityHeaders::class,
        ]);

        $middleware->alias(['admin' => EnsureUserIsAdmin::class]);

        // The only login page is the admin one.
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // A lost database connection is an outage, not a bug, so even in
        // debug mode answer with the "back soon" page and a 503 (Retry-After
        // tells browsers and crawlers to come back later). The full error
        // is still logged to storage/logs.
        $exceptions->render(function (QueryException $e, Request $request) {
            if (! DatabaseOutage::causedBy($e)) {
                return null;
            }

            $headers = ['Retry-After' => 60];

            return $request->expectsJson()
                ? response()->json(['message' => 'Service temporarily unavailable.'], 503, $headers)
                : response()->view('errors.503', ['errors' => new ViewErrorBag], 503, $headers);
        });
    })->create();
