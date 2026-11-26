<?php

namespace App\Http\Controllers\Organization;

use App\Enums\CustomerType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreCustomerRequest;
use App\Http\Requests\Organization\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\AuditLogService;
use App\Services\OrganizationContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(
        protected OrganizationContextService $context,
        protected AuditLogService $auditLog
    ) {}

    public function index(Request $request): View
    {
        $org = $this->context->current();

        $query = Customer::forOrganization($org->id);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('customer_type')) {
            $query->where('customer_type', $type);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $customers = $query->latest()->paginate(15)->withQueryString();
        $customerTypes = CustomerType::cases();

        return view('organization.customers.index', compact('customers', 'org', 'customerTypes'));
    }

    public function create(): View
    {
        $org = $this->context->current();
        $customerTypes = CustomerType::cases();

        return view('organization.customers.create', compact('org', 'customerTypes'));
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $org = $this->context->current();
        $data = $request->validated();

        $data['organization_id'] = $org->id;
        $data['created_by'] = Auth::id();

        $customer = Customer::create($data);

        $this->auditLog->log(
            action: 'customer.created',
            module: 'customers',
            entityType: Customer::class,
            entityId: $customer->id,
            newValues: ['name' => $customer->name, 'email' => $customer->email, 'type' => $customer->customer_type]
        );

        return redirect()->route('organization.customers.index')->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer): View
    {
        $org = $this->context->current();

        if ($customer->organization_id !== $org->id) {
            abort(403, 'Unauthorized. This customer does not belong to your organization.');
        }

        $customer->load(['creator']);

        return view('organization.customers.show', compact('customer', 'org'));
    }

    public function edit(Customer $customer): View
    {
        $org = $this->context->current();

        if ($customer->organization_id !== $org->id) {
            abort(403, 'Unauthorized. This customer does not belong to your organization.');
        }

        $customerTypes = CustomerType::cases();

        return view('organization.customers.edit', compact('customer', 'org', 'customerTypes'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $org = $this->context->current();

        if ($customer->organization_id !== $org->id) {
            abort(403, 'Unauthorized. This customer does not belong to your organization.');
        }

        $oldValues = ['name' => $customer->name, 'status' => $customer->status->value];
        $customer->update($request->validated());

        $this->auditLog->log(
            action: 'customer.updated',
            module: 'customers',
            entityType: Customer::class,
            entityId: $customer->id,
            oldValues: $oldValues,
            newValues: ['name' => $customer->name, 'status' => $customer->status->value]
        );

        return redirect()->route('organization.customers.show', $customer)->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $org = $this->context->current();

        if ($customer->organization_id !== $org->id) {
            abort(403, 'Unauthorized. This customer does not belong to your organization.');
        }

        $this->auditLog->log(
            action: 'customer.deleted',
            module: 'customers',
            entityType: Customer::class,
            entityId: $customer->id,
            oldValues: ['name' => $customer->name]
        );

        $customer->delete();

        return redirect()->route('organization.customers.index')->with('success', 'Customer deleted successfully.');
    }
}
