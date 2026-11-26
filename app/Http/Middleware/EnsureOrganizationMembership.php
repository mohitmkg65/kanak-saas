<?php

namespace App\Http\Middleware;

use App\Services\OrganizationContextService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationMembership
{
    public function __construct(
        protected OrganizationContextService $context
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $currentOrg = $this->context->current();

        if (! $user || ! $currentOrg) {
            return redirect()->route('login');
        }

        if (! $user->isMemberOf($currentOrg->id) && ! $user->isSuperAdmin()) {
            $this->context->clear();
            abort(403, 'Unauthorized. Membership required for active organization.');
        }

        return $next($request);
    }
}
