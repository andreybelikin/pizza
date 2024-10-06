<?php

namespace App\Http\Requests\Cart;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class CartProductsDeleteRequest extends FormRequest
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
            'products.*.id' => 'required|integer'
        ];
    }

    public function messages(): array
    {
        return [
            'products.required' => 'A products array is required',
            'products.array' => 'A products type must be an array',
            'products.min' => 'A products array must not be empty',
            'products.*.id.required' => 'A product id is required',
            'products.*.id.integer' => 'A product id type must be integer',
        ];
    }

    protected function failedValidation(Validator $validator): JsonResponse
    {
        throw new ValidationException($validator);
    }
}
