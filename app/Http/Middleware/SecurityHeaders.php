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

        // While `npm run dev` is running, styles are served by the Vite dev server.
        $vite = Vite::isRunningHot() ? ' '.$this->viteOrigin() : '';

        $response->headers->add([
            'Content-Security-Policy' => implode('; ', [
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
            ]),
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
        ]);

        return $response;
    }

    private function viteOrigin(): string
    {
        $url = parse_url(trim((string) @file_get_contents(public_path('hot'))));

        return ($url['scheme'] ?? 'http').'://'.($url['host'] ?? 'localhost').(isset($url['port']) ? ':'.$url['port'] : '');
    }
}
