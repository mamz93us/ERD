<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(function ($request) {
            // Driver portal lives under /driver/* — guests bounce to its login.
            if ($request->is('driver') || $request->is('driver/*')) {
                return route('driver.login');
            }

            // Admin / Filament redirects handled by Filament itself.
            return null;
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
