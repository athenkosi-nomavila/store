<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /** Only admins reach this request; the route middleware checks that. */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sku' => strtoupper(trim((string) $this->input('sku'))),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var Product|null $product */
        $product = $this->route('product');

        return [
            'name' => ['required', 'string', 'max:150'],
            'sku' => ['required', 'string', 'max:32', 'regex:/^[A-Z0-9-]+$/', Rule::unique('products', 'sku')->ignore($product)],
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'price' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:99999999.99'],
            'quantity' => ['required', 'integer', 'min:0', 'max:1000000'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['boolean'],
            // Real JPG, PNG or WebP pictures only (checked from the file's
            // contents, not its name). SVG is refused: it can carry scripts.
            'image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'sku.regex' => 'The SKU may only use capital letters, numbers and dashes.',
            'image.mimes' => 'The image must be a JPG, PNG or WebP picture.',
            'image.max' => 'The image must be 2 MB or smaller.',
            'image.dimensions' => 'The image must be between 100 and 4000 pixels on each side.',
        ];
    }
}
