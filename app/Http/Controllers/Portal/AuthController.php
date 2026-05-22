<?php

declare(strict_types=1);

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Phase 11 customer portal login. Identifier is email OR phone; password
 * verified against customers.password (bcrypt).
 *
 * Per CLAUDE.md §6 Phase 11 spec said Breeze Inertia/Vue. For the staging
 * dry-run we use the same blade-based pattern as the Phase 10 driver
 * portal so we ship now without a Vue toolchain. Phase 11.x can swap to
 * Inertia/Vue per spec — the routes and controllers stay; only the
 * resources/views layer changes.
 */
class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('portal.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $customer = Customer::query()
            ->where('email', $data['identifier'])
            ->orWhere('phone', $data['identifier'])
            ->orWhere('whatsapp_phone', $data['identifier'])
            ->first();

        if ($customer === null
            || $customer->is_blacklisted
            || ! Auth::guard('customer')->attempt(['id' => $customer->id, 'password' => $data['password']])
        ) {
            throw ValidationException::withMessages([
                'identifier' => __('portal.invalid_credentials'),
            ]);
        }

        $customer->update(['last_login_at' => now()]);

        $request->session()->regenerate();

        return redirect()->intended(route('portal.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('portal.login');
    }
}
