<?php

declare(strict_types=1);

it('boots and uses the Adly Group Agency app name', function () {
    expect(config('app.name'))->toBe('Adly Group Agency');
});

it('uses Africa/Cairo timezone', function () {
    expect(config('app.timezone'))->toBe('Africa/Cairo');
});

it('defaults to Arabic locale with English fallback', function () {
    expect(config('app.locale'))->toBe('ar')
        ->and(config('app.fallback_locale'))->toBe('en');
});
