<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::with(['organization']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if ($organizationId = $request->input('organization_id')) {
            $query->where('organization_id', $organizationId);
        }

        if ($type = $request->input('customer_type')) {
            $query->where('customer_type', $type);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $customers = $query->latest()->paginate(20)->withQueryString();
        $organizations = Organization::approved()->orderBy('name')->get();

        return view('admin.customers.index', compact('customers', 'organizations'));
    }

    public function show(Customer $customer): View
    {
        $customer->load(['organization', 'creator']);

        return view('admin.customers.show', compact('customer'));
    }
}
