<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrganizationApprovalStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrganizationRequest;
use App\Http\Requests\Admin\UpdateOrganizationRequest;
use App\Models\Organization;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function __construct(protected AuditLogService $auditLog) {}

    public function index(Request $request): View
    {
        $query = Organization::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($approvalStatus = $request->input('approval_status')) {
            $query->where('approval_status', $approvalStatus);
        }

        $organizations = $query->withCount('memberships')->latest()->paginate(15)->withQueryString();
        $approvalStatuses = OrganizationApprovalStatus::cases();

        return view('admin.organizations.index', compact('organizations', 'approvalStatuses'));
    }

    public function create(): View
    {
        return view('admin.organizations.create');
    }

    public function store(StoreOrganizationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['approval_status'] = OrganizationApprovalStatus::Approved->value;
        $data['approved_by'] = Auth::id();
        $data['approved_at'] = now();

        $organization = Organization::create($data);

        $this->auditLog->log(
            action: 'organization.created',
            module: 'organizations',
            entityType: Organization::class,
            entityId: $organization->id,
            newValues: ['name' => $organization->name, 'slug' => $organization->slug]
        );

        return redirect()->route('admin.organizations.index')->with('success', 'Organization created successfully.');
    }

    public function show(Organization $organization): View
    {
        $organization->load(['memberships.user', 'roles', 'approvedBy', 'customers']);
        $approvalStatuses = OrganizationApprovalStatus::cases();

        return view('admin.organizations.show', compact('organization', 'approvalStatuses'));
    }

    public function edit(Organization $organization): View
    {
        return view('admin.organizations.edit', compact('organization'));
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $oldValues = ['name' => $organization->name, 'status' => $organization->status->value];
        $organization->update($data);

        $this->auditLog->log(
            action: 'organization.updated',
            module: 'organizations',
            entityType: Organization::class,
            entityId: $organization->id,
            oldValues: $oldValues,
            newValues: ['name' => $organization->name, 'status' => $organization->status->value]
        );

        return redirect()->route('admin.organizations.index')->with('success', 'Organization updated successfully.');
    }

    public function approve(Organization $organization): RedirectResponse
    {
        $organization->update([
            'approval_status' => OrganizationApprovalStatus::Approved->value,
            'status' => 'active',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        $this->auditLog->log(
            action: 'organization.approved',
            module: 'organizations',
            entityType: Organization::class,
            entityId: $organization->id,
            newValues: ['approval_status' => 'approved']
        );

        return redirect()->route('admin.organizations.show', $organization)->with('success', 'Organization has been approved successfully.');
    }

    public function reject(Request $request, Organization $organization): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $organization->update([
            'approval_status' => OrganizationApprovalStatus::Rejected->value,
            'status' => 'inactive',
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        $this->auditLog->log(
            action: 'organization.rejected',
            module: 'organizations',
            entityType: Organization::class,
            entityId: $organization->id,
            newValues: ['approval_status' => 'rejected', 'reason' => $request->input('rejection_reason')]
        );

        return redirect()->route('admin.organizations.show', $organization)->with('success', 'Organization has been rejected.');
    }

    public function markUnderReview(Organization $organization): RedirectResponse
    {
        $organization->update([
            'approval_status' => OrganizationApprovalStatus::UnderReview->value,
        ]);

        $this->auditLog->log(
            action: 'organization.under_review',
            module: 'organizations',
            entityType: Organization::class,
            entityId: $organization->id,
            newValues: ['approval_status' => 'under_review']
        );

        return redirect()->route('admin.organizations.show', $organization)->with('success', 'Organization marked as Under Review.');
    }

    public function suspend(Request $request, Organization $organization): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10', 'max:1000'],
        ]);

        $organization->update([
            'approval_status' => OrganizationApprovalStatus::Suspended->value,
            'status' => 'suspended',
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        $this->auditLog->log(
            action: 'organization.suspended',
            module: 'organizations',
            entityType: Organization::class,
            entityId: $organization->id,
            newValues: ['approval_status' => 'suspended', 'reason' => $request->input('rejection_reason')]
        );

        return redirect()->route('admin.organizations.show', $organization)->with('success', 'Organization has been suspended.');
    }
}
