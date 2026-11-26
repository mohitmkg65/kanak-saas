<?php

namespace App\Http\Controllers\Organization;

use App\Enums\RoleScope;
use App\Http\Controllers\Controller;
use App\Services\OrganizationContextService;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        protected OrganizationContextService $context,
        protected SettingService $settingService
    ) {}

    public function index(): View
    {
        $org = $this->context->current();
        $settings = $this->settingService->getAllForScope(RoleScope::ORGANIZATION, $org->id);

        return view('organization.settings.index', compact('org', 'settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $org = $this->context->current();

        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'business_email' => ['nullable', 'email', 'max:255'],
            'business_phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'currency' => ['required', 'string', 'max:10'],
            'timezone' => ['required', 'string', 'max:100'],
            'locale' => ['required', 'string', 'max:10'],
        ]);

        $org->update([
            'name' => $validated['business_name'],
            'legal_name' => $validated['legal_name'] ?? null,
            'email' => $validated['business_email'] ?? null,
            'phone' => $validated['business_phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'country' => $validated['country'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'currency' => $validated['currency'],
            'timezone' => $validated['timezone'],
            'locale' => $validated['locale'],
        ]);

        $this->settingService->updateMany($validated, RoleScope::ORGANIZATION, $org->id, 'business');

        return back()->with('success', 'Organization settings updated successfully.');
    }
}
