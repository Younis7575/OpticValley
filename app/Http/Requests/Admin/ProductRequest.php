<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('products') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['slug' => Str::slug($this->input('slug') ?: $this->input('name', ''))]);
    }

    public function rules(): array
    {
        $id = $this->route('product')?->id;

        return [
            'name' => 'required|string|max:200',
            'slug' => ['required', 'string', 'max:200', Rule::unique('products', 'slug')->ignore($id)],
            'sku' => ['required', 'string', 'max:60', Rule::unique('products', 'sku')->ignore($id)],
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'extra_categories' => 'nullable|array',
            'extra_categories.*' => 'exists:categories,id',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:20000',
            'price' => 'required|numeric|min:0|max:9999999',
            'sale_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0|max:1000000',
            'low_stock_threshold' => 'required|integer|min:0|max:10000',
            'gender' => ['required', Rule::in(array_keys(Product::GENDERS))],
            'frame_shape' => 'nullable|string|max:40',
            'frame_material' => 'nullable|string|max:40',
            'frame_color' => 'nullable|string|max:40',
            'frame_size' => 'nullable|string|max:40',
            'lens_info' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'images' => 'nullable|array|max:12',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
            'image_order' => 'nullable|array',
            'image_alt' => 'nullable|array',
            'variants' => 'nullable|array|max:30',
            'variants.*.id' => 'nullable|integer',
            'variants.*.name' => 'nullable|string|max:80',
            'variants.*.color_hex' => ['nullable', 'regex:/^#[0-9a-fA-F]{3,8}$/'],
            'variants.*.sku' => 'nullable|string|max:60',
            'variants.*.price_adjustment' => 'nullable|numeric|min:-999999|max:999999',
            'variants.*.stock' => 'nullable|integer|min:0',
            'spec_keys' => 'nullable|array',
            'spec_values' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return ['sale_price.lt' => 'The sale price must be lower than the regular price.'];
    }
}
