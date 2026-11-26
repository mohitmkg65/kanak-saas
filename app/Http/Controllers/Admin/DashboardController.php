<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrganizationApprovalStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Organization;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_organizations' => Organization::count(),
            'active_organizations' => Organization::where('approval_status', OrganizationApprovalStatus::Approved->value)->count(),
            'pending_organizations' => Organization::where('approval_status', OrganizationApprovalStatus::Pending->value)
                ->orWhere('approval_status', OrganizationApprovalStatus::UnderReview->value)
                ->count(),
            'total_system_users' => User::count(),
            'total_categories' => Category::count(),
            'total_products' => Product::count(),
            'total_customers' => Customer::count(),
        ];

        $recentOrganizations = Organization::withCount('memberships')->latest()->take(5)->get();
        $pendingOrganizations = Organization::where('approval_status', OrganizationApprovalStatus::Pending->value)->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrganizations', 'pendingOrganizations'));
    }
}
