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
            'title' => 'required_without_all:description,type,address,price|string|max:50|',
            'description' => 'required_without_all:title,type,address,price|string|max:250|',
            'type' => ['required_without_all:title,description,type,address,price', new Enum(ProductType::class)],
            'price' => 'required_without_all:title,description,type,address|integer',
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
            'id.max' => 'A product id is only :max char long',
            'title.unique' => 'A product title must be unique',
            'price.max' => 'A product price is :max max',
            'price.integer' => 'A product price must be an integer',
            'description.string' => 'A product description must be a string',
            'description.max' => 'A product description is only :max char long',
            'description.unique' => 'A product description must be unique',
            'type.in_enum' => 'A product type is invalid',
            '*.required_without_all' => 'At least one field is required',
        ];
    }

    protected function failedValidation(Validator $validator): JsonResponse
    {
        throw new ValidationException($validator);
    }
}
