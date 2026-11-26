<?php

namespace App\Http\Requests\Organization;

use App\Services\OrganizationContextService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrganizationProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $orgId = app(OrganizationContextService::class)->current()->id;

        return [
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('organization_products')->where('organization_id', $orgId),
            ],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'hsn_code' => ['nullable', 'string', 'max:20'],
            'brand' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'selling_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'purchase_price' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'mrp' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'opening_stock' => ['nullable', 'numeric', 'min:0'],
            'min_stock_level' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sku.unique' => 'This SKU is already used by another product in your organization.',
            'selling_price.min' => 'Selling price cannot be negative.',
            'purchase_price.min' => 'Purchase price cannot be negative.',
        ];
    }
}
