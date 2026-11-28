<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrganizationApprovalStatus;
use App\Enums\OrganizationStatus;
use App\Enums\OrganizationSubscriptionStatus;
use App\Enums\RegistrationSource;
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

        if ($source = $request->input('registration_source')) {
            $query->where('registration_source', $source);
        }

        if ($subStatus = $request->input('subscription_status')) {
            $query->where('subscription_status', $subStatus);
        }

        $organizations = $query->withCount('memberships')->latest()->paginate(15)->withQueryString();
        $approvalStatuses = OrganizationApprovalStatus::cases();
        $registrationSources = RegistrationSource::cases();
        $subscriptionStatuses = OrganizationSubscriptionStatus::cases();

        return view('admin.organizations.index', compact('organizations', 'approvalStatuses', 'registrationSources', 'subscriptionStatuses'));
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
        $data['subscription_status'] = OrganizationSubscriptionStatus::Trialing->value;
        $data['registration_source'] = RegistrationSource::AdminCreated->value;
        $data['created_by'] = Auth::id();
        $data['approved_by'] = Auth::id();
        $data['approved_at'] = now();

        $organization = Organization::create($data);

        // Record lifecycle log
        $organization->statusLogs()->create([
            'action' => 'registered',
            'from_status' => null,
            'to_status' => $organization->status->value ?? 'active',
            'from_approval_status' => null,
            'to_approval_status' => OrganizationApprovalStatus::Approved->value,
            'reason' => 'Organization created directly by Super Admin.',
            'action_by' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

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
        $organization->load(['memberships.user', 'roles', 'approvedBy', 'createdBy', 'customers', 'statusLogs.actionBy']);
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

    public function approve(Request $request, Organization $organization): RedirectResponse
    {
        $fromStatus = $organization->status?->value ?? (string) $organization->status;
        $fromApproval = $organization->approval_status?->value ?? (string) $organization->approval_status;

        $organization->update([
            'approval_status' => OrganizationApprovalStatus::Approved->value,
            'status' => OrganizationStatus::ACTIVE->value,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        $remarks = $request->input('remarks') ?: 'Organization application approved by Super Admin.';

        $organization->statusLogs()->create([
            'action' => 'approved',
            'from_status' => $fromStatus,
            'to_status' => OrganizationStatus::ACTIVE->value,
            'from_approval_status' => $fromApproval,
            'to_approval_status' => OrganizationApprovalStatus::Approved->value,
            'reason' => $remarks,
            'action_by' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
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

    public function reapprove(Request $request, Organization $organization): RedirectResponse
    {
        $request->validate([
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $fromStatus = $organization->status?->value ?? (string) $organization->status;
        $fromApproval = $organization->approval_status?->value ?? (string) $organization->approval_status;

        $organization->update([
            'approval_status' => OrganizationApprovalStatus::Approved->value,
            'status' => OrganizationStatus::ACTIVE->value,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        $remarks = $request->input('remarks') ?: 'Organization re-approved by Super Admin after review.';

        $organization->statusLogs()->create([
            'action' => 'reapproved',
            'from_status' => $fromStatus,
            'to_status' => OrganizationStatus::ACTIVE->value,
            'from_approval_status' => $fromApproval,
            'to_approval_status' => OrganizationApprovalStatus::Approved->value,
            'reason' => $remarks,
            'action_by' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $this->auditLog->log(
            action: 'organization.reapproved',
            module: 'organizations',
            entityType: Organization::class,
            entityId: $organization->id,
            newValues: ['approval_status' => 'approved', 'remarks' => $remarks]
        );

        return redirect()->route('admin.organizations.show', $organization)->with('success', 'Organization has been re-approved successfully.');
    }

    public function reject(Request $request, Organization $organization): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $fromStatus = $organization->status?->value ?? (string) $organization->status;
        $fromApproval = $organization->approval_status?->value ?? (string) $organization->approval_status;
        $reason = $request->input('rejection_reason');

        $organization->update([
            'approval_status' => OrganizationApprovalStatus::Rejected->value,
            'status' => OrganizationStatus::INACTIVE->value,
            'rejection_reason' => $reason,
        ]);

        $organization->statusLogs()->create([
            'action' => 'rejected',
            'from_status' => $fromStatus,
            'to_status' => OrganizationStatus::INACTIVE->value,
            'from_approval_status' => $fromApproval,
            'to_approval_status' => OrganizationApprovalStatus::Rejected->value,
            'reason' => $reason,
            'action_by' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $this->auditLog->log(
            action: 'organization.rejected',
            module: 'organizations',
            entityType: Organization::class,
            entityId: $organization->id,
            newValues: ['approval_status' => 'rejected', 'reason' => $reason]
        );

        return redirect()->route('admin.organizations.show', $organization)->with('success', 'Organization has been rejected.');
    }

    public function markUnderReview(Request $request, Organization $organization): RedirectResponse
    {
        $fromStatus = $organization->status?->value ?? (string) $organization->status;
        $fromApproval = $organization->approval_status?->value ?? (string) $organization->approval_status;

        $organization->update([
            'approval_status' => OrganizationApprovalStatus::UnderReview->value,
        ]);

        $organization->statusLogs()->create([
            'action' => 'under_review',
            'from_status' => $fromStatus,
            'to_status' => $fromStatus,
            'from_approval_status' => $fromApproval,
            'to_approval_status' => OrganizationApprovalStatus::UnderReview->value,
            'reason' => 'Application marked under review by admin.',
            'action_by' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
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
            'suspension_reason' => ['required_without:rejection_reason', 'nullable', 'string', 'min:5', 'max:1000'],
            'rejection_reason' => ['nullable', 'string', 'min:5', 'max:1000'],
        ]);

        $reason = $request->input('suspension_reason') ?? $request->input('rejection_reason');
        $fromStatus = $organization->status?->value ?? (string) $organization->status;
        $fromApproval = $organization->approval_status?->value ?? (string) $organization->approval_status;

        $organization->update([
            'status' => OrganizationStatus::SUSPENDED->value,
        ]);

        $organization->statusLogs()->create([
            'action' => 'suspended',
            'from_status' => $fromStatus,
            'to_status' => OrganizationStatus::SUSPENDED->value,
            'from_approval_status' => $fromApproval,
            'to_approval_status' => $fromApproval,
            'reason' => $reason,
            'action_by' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $this->auditLog->log(
            action: 'organization.suspended',
            module: 'organizations',
            entityType: Organization::class,
            entityId: $organization->id,
            newValues: ['status' => 'suspended', 'reason' => $reason]
        );

        return redirect()->route('admin.organizations.show', $organization)->with('success', 'Organization has been suspended.');
    }

    public function reactivate(Request $request, Organization $organization): RedirectResponse
    {
        $request->validate([
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $fromStatus = $organization->status?->value ?? (string) $organization->status;
        $fromApproval = $organization->approval_status?->value ?? (string) $organization->approval_status;
        $remarks = $request->input('remarks') ?: 'Organization account reactivated by Super Admin.';

        $organization->update([
            'status' => OrganizationStatus::ACTIVE->value,
        ]);

        $organization->statusLogs()->create([
            'action' => 'reactivated',
            'from_status' => $fromStatus,
            'to_status' => OrganizationStatus::ACTIVE->value,
            'from_approval_status' => $fromApproval,
            'to_approval_status' => $fromApproval,
            'reason' => $remarks,
            'action_by' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $this->auditLog->log(
            action: 'organization.reactivated',
            module: 'organizations',
            entityType: Organization::class,
            entityId: $organization->id,
            newValues: ['status' => 'active', 'remarks' => $remarks]
        );

        return redirect()->route('admin.organizations.show', $organization)->with('success', 'Organization has been reactivated successfully.');
    }
}
