<?php

namespace App\Http\Requests\Cart;

use App\Enums\ProductType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;

class CartAddRequest extends FormRequest
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
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'products.required' => 'A products must be set',
            'products.array' => 'A products must be array',
            'products.*.id.required' => 'A product id must be set',
            'products.*.id.integer' => 'A product id must be an integer',
            'products.*.id.exists' => 'A product is not exists',
            'products.*.quantity.required' => 'A product quantity must be set',
            'products.*.quantity.integer' => 'A product quantity must be integer',
            'products.*.quantity.min' => 'A product quantity must be > 1',
        ];
    }

    protected function failedValidation(Validator $validator): JsonResponse
    {
        throw new ValidationException($validator);
    }
}
