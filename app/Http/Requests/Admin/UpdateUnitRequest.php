<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $unitId = $this->route('unit')?->id;

        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('units', 'name')->ignore($unitId)],
            'abbreviation' => ['required', 'string', 'max:20', Rule::unique('units', 'abbreviation')->ignore($unitId)],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ];
    }
}
