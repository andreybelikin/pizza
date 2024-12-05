<?php

namespace App\Http\Requests\Cart;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CartUpdateRequest extends FormRequest
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
            'products.*.id' => 'required|integer|exists:products,id|distinct',
            'products.*.quantity' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'products.required' => 'A products must be set',
            'products.array' => 'A products must be array',
            'products.min' => 'A products must be at least 1',
            'products.*.id.required' => 'A product id must be set',
            'products.*.id.integer' => 'A product id must be an integer',
            'products.*.id.exists' => 'A product is not exists',
            'products.*.id.distinct' => 'A product id in request must be unique',
            'products.*.quantity.required' => 'A product quantity must be set',
            'products.*.quantity.integer' => 'A product quantity must be integer',
        ];
    }

    protected function failedValidation(Validator $validator): JsonResponse
    {
        throw new ValidationException($validator);
    }
}
