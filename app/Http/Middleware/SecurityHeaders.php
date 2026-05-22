<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Phase 13: security headers applied to every web response.
 *
 *  - X-Frame-Options: DENY            (no embedding in frames)
 *  - X-Content-Type-Options: nosniff  (no MIME sniffing)
 *  - Referrer-Policy: same-origin
 *  - Permissions-Policy: tight default — disable geolocation/camera/etc.
 *    that the app doesn't need. (Driver portal can opt back in for the
 *    receipt-photo upload via a route-specific override later.)
 *  - Strict-Transport-Security on HTTPS only.
 *  - Content-Security-Policy: pragmatic for Filament + Tailwind CDN
 *    used by /driver and /portal. Allows inline styles + scripts because
 *    Filament Livewire injects inline handlers; revisit when Filament v5
 *    ships a CSP-strict mode.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'same-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(self), payment=(), usb=()');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Allow Tailwind CDN + Google Fonts (used by driver/portal layouts),
        // plus inline styles/scripts (Filament+Livewire need them).
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.tailwindcss.com",
            "font-src 'self' data: https://fonts.gstatic.com",
            "img-src 'self' data: blob:",
            "connect-src 'self'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ];
        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        return $response;
    }
}
