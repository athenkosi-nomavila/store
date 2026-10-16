<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds browser-side defences to every web response. The storefront has no
 * inline scripts, so the Content Security Policy can forbid them.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Laravel's debug error page (APP_DEBUG=true, uncaught exceptions,
        // always a 500) is built from inline styles and scripts, so the
        // policy below would break it.
        $debugPage = config('app.debug') && $response->getStatusCode() === 500
            && isset($response->exception);

        // While `npm run dev` is running, styles are served by the Vite dev server.
        $vite = Vite::isRunningHot() ? ' '.$this->viteOrigin() : '';

        if (! $debugPage) {
            $response->headers->set('Content-Security-Policy', $this->policy($vite));
        }

        $response->headers->add([
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
        ]);

        return $response;
    }

    private function policy(string $vite): string
    {
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self'{$vite}",
            "style-src 'self' https://fonts.googleapis.com{$vite}",
            "font-src 'self' https://fonts.gstatic.com",
            "connect-src 'self'".($vite ? $vite.' '.str_replace('http', 'ws', trim($vite)) : ''),
            "img-src 'self' data:",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "object-src 'none'",
        ]);
    }

    private function viteOrigin(): string
    {
        $url = parse_url(trim((string) @file_get_contents(public_path('hot'))));

        return ($url['scheme'] ?? 'http').'://'.($url['host'] ?? 'localhost').(isset($url['port']) ? ':'.$url['port'] : '');
    }
}
