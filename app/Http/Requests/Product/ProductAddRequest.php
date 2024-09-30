<?php

namespace App\Http\Requests\Product;

use App\Enums\ProductType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;

class ProductAddRequest extends FormRequest
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
            'title' => 'required|string|max:50|unique:products',
            'description' => 'required|string|max:250|unique:products',
            'type' => ['required', new Enum(ProductType::class)],
            'price' => 'required|integer|max:8000',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'A product id is required',
            'id.string' => 'A product id must to be string',
            'id.max' => 'A product id is only 36 char long',
            'price.max' => 'A product price is 8000 max',
            'price.required' => 'A product price is 8000 max',
            'price.integer' => 'A product price must be an integer',
            'title.required' => 'A product title is required',
            'title.unique' => 'A product title must be unique',
            'description.required' => 'A product description is required',
            'description.string' => 'A product description must be a string',
            'description.unique' => 'A product description must be unique',
            'description.max' => 'A product description is only 250 char long',
            'type.in_enum' => 'A product type is invalid',
            'type.required' => 'A product type is required',
        ];
    }

    protected function failedValidation(Validator $validator): JsonResponse
    {
        throw new ValidationException($validator);
    }
}
