<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Services\OrganizationContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrganizationSelectController extends Controller
{
    public function __construct(
        protected OrganizationContextService $context
    ) {}

    public function showSelectForm(): View|RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $memberships = $user->activeMemberships()
            ->whereHas('organization', function ($q) {
                $q->where('status', 'active');
            })
            ->with('organization')
            ->get();

        if ($memberships->isEmpty() && ! $user->isSuperAdmin()) {
            Auth::logout();

            return redirect()->route('login')->withErrors(['email' => 'No active organization memberships found.']);
        }

        return view('auth.select-organization', [
            'memberships' => $memberships,
        ]);
    }

    public function select(Request $request): RedirectResponse
    {
        $request->validate([
            'organization_id' => ['required', 'string', 'exists:organizations,public_id'],
        ]);

        $user = Auth::user();
        $org = Organization::where('public_id', $request->input('organization_id'))->firstOrFail();

        if (! $this->context->set($org, $user)) {
            return back()->withErrors(['organization_id' => 'You do not have active membership access to this organization.']);
        }

        return redirect()->intended(route('organization.dashboard'));
    }
}
