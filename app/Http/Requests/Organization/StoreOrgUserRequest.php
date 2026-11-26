<?php

namespace App\Http\Requests\Organization;

use App\Enums\RoleScope;
use App\Enums\UserStatus;
use App\Services\OrganizationContextService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrgUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $orgId = app(OrganizationContextService::class)->id();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'status' => ['required', 'string', 'in:'.implode(',', array_column(UserStatus::cases(), 'value'))],
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where(function ($query) use ($orgId) {
                    $query->where('scope', RoleScope::ORGANIZATION->value)
                        ->where('organization_id', $orgId)
                        ->where('status', 'active');
                }),
            ],
        ];
    }
}
