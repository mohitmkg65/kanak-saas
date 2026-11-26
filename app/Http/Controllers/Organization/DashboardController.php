<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\OrganizationProduct;
use App\Models\User;
use App\Services\OrganizationContextService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected OrganizationContextService $context
    ) {}

    public function index(): View
    {
        $org = $this->context->current();
        $user = Auth::user();

        $stats = [
            'organization_name' => $org->name,
            'current_user' => $user->name,
            'current_plan' => 'Enterprise Foundation',
            'system_users_count' => User::whereHas('memberships', function ($q) use ($org) {
                $q->where('organization_id', $org->id)->where('status', 'active');
            })->count(),
            'products_count' => OrganizationProduct::where('organization_id', $org->id)->count(),
            'customers_count' => Customer::where('organization_id', $org->id)->count(),
        ];

        return view('organization.dashboard', compact('stats', 'org', 'user'));
    }
}
