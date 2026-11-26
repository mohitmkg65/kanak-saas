<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleScope;
use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        protected SettingService $settingService
    ) {}

    public function index(): View
    {
        $settings = $this->settingService->getAllForScope(RoleScope::SUPER_ADMIN);

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'platform_name' => ['required', 'string', 'max:255'],
            'support_email' => ['required', 'email', 'max:255'],
            'support_phone' => ['nullable', 'string', 'max:50'],
            'default_timezone' => ['required', 'string', 'max:100'],
            'default_currency' => ['required', 'string', 'max:10'],
        ]);

        $this->settingService->updateMany($validated, RoleScope::SUPER_ADMIN, null, 'general');

        return back()->with('success', 'Platform settings updated successfully.');
    }
}
