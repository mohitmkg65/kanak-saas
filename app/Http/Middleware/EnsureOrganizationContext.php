<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use App\Services\OrganizationContextService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationContext
{
    public function __construct(
        protected OrganizationContextService $context
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || $user->status !== UserStatus::ACTIVE) {
            Auth::logout();

            return redirect()->route('login')->withErrors(['email' => 'Your account is inactive or suspended.']);
        }

        if (! $this->context->hasContext()) {
            $activeMemberships = $user->activeMemberships()->with('organization')->get();

            if ($activeMemberships->isEmpty()) {
                if ($user->isSuperAdmin()) {
                    return redirect()->route('admin.dashboard');
                }

                Auth::logout();

                return redirect()->route('login')->withErrors(['email' => 'You do not belong to any active organization.']);
            }

            if ($activeMemberships->count() === 1) {
                $org = $activeMemberships->first()->organization;

                if ($org && $org->status->value === 'active') {
                    $this->context->set($org, $user);
                } else {
                    Auth::logout();

                    return redirect()->route('login')->withErrors(['email' => 'Your organization account is not active.']);
                }
            } else {
                return redirect()->route('organization.select');
            }
        }

        return $next($request);
    }
}
