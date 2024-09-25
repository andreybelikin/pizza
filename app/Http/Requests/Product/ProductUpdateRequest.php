<?php

namespace App\Http\Requests\Product;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProductUpdateRequest extends FormRequest
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
            'id' => 'required|string|max:36',
            'title' => 'unique:products|string|max:50',
            'description' => 'string|max:250',
            'type' => 'string|max:25',
            'price' => 'integer|max:8000',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    public function messages(): array
    {
        return [
            'id.required' => 'A product id is required',
            'id.string' => 'A product id must be a string',
            'id.max' => 'A product id is only 36 char long',
            'price.max' => 'A product price is 8000 max',
            'price.integer' => 'A product price must be an integer',
            'description.string' => 'A product description must be a string',
            'description.max' => 'A product description is only 250 char long',
            'type.string' => 'A product type must be a string',
            'type.max' => 'A product type is only 25 char long',
            'title.unique' => 'A product title must be unique',
        ];
    }

    protected function failedValidation(Validator $validator): JsonResponse
    {
        throw new ValidationException($validator);
    }
}
