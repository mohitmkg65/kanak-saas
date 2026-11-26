<?php

namespace App\Http\Requests\Admin;

use App\Enums\RoleScope;
use App\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'status' => ['required', 'string', 'in:'.implode(',', array_column(UserStatus::cases(), 'value'))],
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where(function ($query) {
                    $query->where('scope', RoleScope::SUPER_ADMIN->value)
                        ->where('status', 'active');
                }),
            ],
        ];
    }
}
