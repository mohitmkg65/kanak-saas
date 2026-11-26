<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdminContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || $user->status !== UserStatus::ACTIVE) {
            Auth::logout();

            return redirect()->route('login')->withErrors(['email' => 'Your account is inactive or suspended.']);
        }

        if (! $user->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super Admin access required.');
        }

        return $next($request);
    }
}
