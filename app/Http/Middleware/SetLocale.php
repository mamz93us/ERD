<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the request locale in this order:
 *   1. `?locale=ar|en` query (sets session and redirects clean)
 *   2. session `locale` key
 *   3. authenticated user's `preferred_locale`
 *   4. config('app.locale')
 *
 * Public URL-prefix routing (`/ar/...`, `/en/...`) is handled by
 * mcamara/laravel-localization on its own route group and takes precedence
 * over this middleware when active.
 */
class SetLocale
{
    private const SUPPORTED = ['ar', 'en'];

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('locale') && in_array($request->query('locale'), self::SUPPORTED, true)) {
            $request->session()->put('locale', $request->query('locale'));

            return redirect()->to($request->url());
        }

        $locale = $request->session()->get('locale')
            ?? optional($request->user())->preferred_locale
            ?? config('app.locale');

        if (in_array($locale, self::SUPPORTED, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
