<?php

namespace App\Http\Requests\Product;

use App\Enums\ProductType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rules\Enum;
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
            'title' => 'string|max:50|unique:products',
            'description' => 'string|max:250|unique:products',
            'type' => [new Enum(ProductType::class)],
            'price' => 'integer|max:8000',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    protected function withValidator($validator)
    {
        $fields = ['title', 'description', 'type', 'price'];

        $validator->after(function ($validator) use ($fields) {
            if (!$this->anyFilled($fields) || !$this->hasAny($fields)) {
                $validator->errors()->add('fields', 'At least one field must be provided.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'id.required' => 'A product id is required',
            'id.string' => 'A product id must be a string',
            'id.max' => 'A product id is only 36 char long',
            'title.unique' => 'A product title must be unique',
            'price.max' => 'A product price is 8000 max',
            'price.integer' => 'A product price must be an integer',
            'description.string' => 'A product description must be a string',
            'description.max' => 'A product description is only 250 char long',
            'description.unique' => 'A product description must be unique',
            'type.in_enum' => 'A product type is invalid',
        ];
    }

    protected function failedValidation(Validator $validator): JsonResponse
    {
        throw new ValidationException($validator);
    }
}
