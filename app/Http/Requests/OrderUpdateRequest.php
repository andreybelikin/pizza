<?php

namespace App\Http\Requests;

use App\Enums\ProductType;
use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
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
            'orderId' => 'required|integer',
            'phone' => 'string|regex:/^\d{4,15}$/|',
            'name' => 'string|max:20',
            'address' => 'string|max:255',
            'status' => 'string|max:20',
            'orderProducts' => 'nullable|array',
            'orderProducts.*.id' => 'required_with:orderProducts|integer|exists:order_products,id|',
            'orderProducts.*' => [function ($attribute, $value, $fail) {
                $updatableFields = ['name', 'phone', 'description', 'type', 'quantity', 'price'];
                unset($value['id']);

                if (empty(array_filter($value))) {
                    $fail("The {$attribute} must include at least one field besides the ID");
                }
            }],
            'orderProducts.*.quantity' => 'integer',
            'orderProducts.*.title' => 'string|max:50',
            'orderProducts.*.type' => 'enum:' . ProductType::class,
            'orderProducts.*.description' => 'string|max:255',
            'orderProducts.*.price' => 'integer',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'orderId' => $this->route('orderId'),
        ]);
    }
}
