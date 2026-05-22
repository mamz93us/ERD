<?php

declare(strict_types=1);

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Phase 10 driver login. Password-based for v1 (spec allows "phone + OTP OR
 * simple password"). OTP flow can layer on top later.
 *
 * Login: phone (or national_id) + password. Match against drivers.password
 * (bcrypt). On success, regenerate session and stamp last_login_at.
 */
class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('driver.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $driver = Driver::query()
            ->where('phone', $data['phone'])
            ->orWhere('whatsapp_phone', $data['phone'])
            ->orWhere('national_id', $data['phone'])
            ->first();

        if ($driver === null || ! Auth::guard('driver')->attempt(['id' => $driver->id, 'password' => $data['password']])) {
            throw ValidationException::withMessages([
                'phone' => __('driver.invalid_credentials'),
            ]);
        }

        $driver->update(['last_login_at' => now()]);

        $request->session()->regenerate();

        return redirect()->intended(route('driver.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('driver')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('driver.login');
    }
}
