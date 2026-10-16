<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validates the public listing filters (search term, sort, category).
 * These are read-only GET parameters, so anyone may submit them.
 */
class ProductListingRequest extends FormRequest
{
    public const SORTS = [
        'newest' => 'Newest',
        'price_asc' => 'Price: low to high',
        'price_desc' => 'Price: high to low',
        'name' => 'Name: A to Z',
    ];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('q'))) {
            $this->merge(['q' => trim($this->input('q'))]);
        }
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', Rule::in(array_keys(self::SORTS))],
            'category' => ['nullable', 'string', 'max:120', Rule::exists('categories', 'slug')],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'q.max' => 'Search terms can be up to 100 characters.',
            'category.exists' => 'That category does not exist.',
        ];
    }

    public function term(): string
    {
        return (string) $this->validated('q', '');
    }

    public function sort(): string
    {
        return $this->validated('sort') ?? 'newest';
    }
}
