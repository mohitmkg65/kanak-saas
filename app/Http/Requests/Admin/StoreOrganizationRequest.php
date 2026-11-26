<?php

namespace App\Http\Requests\Admin;

use App\Enums\OrganizationStatus;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:organizations,slug'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'string', 'in:'.implode(',', array_column(OrganizationStatus::cases(), 'value'))],
            'timezone' => ['required', 'string', 'max:100'],
            'currency' => ['required', 'string', 'max:10'],
            'locale' => ['required', 'string', 'max:10'],
        ];
    }
}
