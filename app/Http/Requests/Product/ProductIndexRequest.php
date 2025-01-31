<?php

namespace App\Http\Requests\Product;

use App\Enums\ProductType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;

class ProductIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => 'nullable|string|max:10',
            'title' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:250',
            'type' => ['nullable', new Enum(ProductType::class)],
            'minPrice' => 'nullable|integer',
            'maxPrice' => 'nullable|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'title.string' => 'A product title must be a string',
            'title.max' => 'A product title is only :max char long',
            'page.max' => 'A page number is only :max char long',
            'minPrice.integer' => 'A product price must be an integer',
            'maxPrice.integer' => 'A product price must be an integer',
            'description.string' => 'A product description must be a string',
            'description.max' => 'A product description is only :max char long',
            'type.in_enum' => 'A product type is invalid',
        ];
    }

    protected function failedValidation(Validator $validator): JsonResponse
    {
        throw new ValidationException($validator);
    }
}
