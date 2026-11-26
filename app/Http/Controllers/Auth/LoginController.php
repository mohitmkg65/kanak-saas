<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\OrganizationContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        protected OrganizationContextService $context
    ) {}

    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->authenticatedRedirect(Auth::user());
        }

        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->status !== UserStatus::ACTIVE) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Your account is inactive or suspended. Please contact support.',
                ]);
            }

            $user->update(['last_login_at' => now()]);

            return $this->authenticatedRedirect($user);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->context->clear();
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    protected function authenticatedRedirect($user): RedirectResponse
    {
        if ($user->isSuperAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        $activeMemberships = $user->activeMemberships()->with('organization')->get();

        if ($activeMemberships->isEmpty()) {
            Auth::logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account does not belong to any active organization.',
            ]);
        }

        if ($activeMemberships->count() === 1) {
            $org = $activeMemberships->first()->organization;

            if ($org && $org->status->value === 'active') {
                $this->context->set($org, $user);

                return redirect()->intended(route('organization.dashboard'));
            }

            Auth::logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Your organization is currently inactive or suspended.',
            ]);
        }

        return redirect()->route('organization.select');
    }
}
