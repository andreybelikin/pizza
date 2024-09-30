<?php

namespace App\Http\Requests\Product;

use App\Enums\ProductType;
use App\Rules\AtLeastOneFieldRequired;
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
            'page' => 'max:10',
            'title' => 'string|max:50',
            'description' => 'string|max:250',
            'type' => [new Enum(ProductType::class)],
            'price' => 'integer|max:8000',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'page' => $this->route('page'),
        ]);
    }

    public function messages(): array
    {
        return [
            'title.string' => 'A product title must be a string',
            'title.max' => 'A product title is only 50 char long',
            'page.max' => 'A page number is only 10 char long',
            'price.max' => 'A product price is 8000 max',
            'price.integer' => 'A product price must be an integer',
            'description.string' => 'A product description must be a string',
            'description.max' => 'A product description is only 250 char long',
        ];
    }

    protected function failedValidation(Validator $validator): JsonResponse
    {
        throw new ValidationException($validator);
    }
}
