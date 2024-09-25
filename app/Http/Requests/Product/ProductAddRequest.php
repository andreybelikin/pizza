<?php

namespace App\Http\Requests\Product;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
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
            'id' => 'required|string|max:36',
            'title' => 'required|unique:products|string|max:50',
            'description' => 'required|string|max:250',
            'type' => 'required|string|max:25',
            'price' => 'required|integer|max:8000',
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
            'id.string' => 'A product id must to be string',
            'id.max' => 'A product id is only 36 char long',
            'price.max' => 'A product price is 8000 max',
            'price.required' => 'A product price is 8000 max',
            'price.integer' => 'A product price must be an integer',
            'title.required' => 'A product title is required',
            'title.unique' => 'A product title must be unique',
            'description.required' => 'A product description is required',
            'description.string' => 'A product description must be a string',
            'description.max' => 'A product description is only 250 char long',
            'type.string' => 'A product type must be a string',
            'type.max' => 'A product type is only 25 char long',
            'type.required' => 'A product type is required',
        ];
    }

    protected function failedValidation(Validator $validator): JsonResponse
    {
        throw new ValidationException($validator);
    }
}
