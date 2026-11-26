<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'hsn_code' => ['nullable', 'string', 'max:20'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku'],
            'brand' => ['nullable', 'string', 'max:255'],
            'base_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ];
    }
}
