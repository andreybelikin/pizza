<?php

namespace App\Http\Requests\Order;

use App\Enums\ProductType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

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
        $this->merge([
            'orderId' => $this->route('orderId'),
        ]);

        return [
            'orderId' => 'required|integer',
            'phone' => 'required_without_all:name,address,status,orderProducts|string|regex:/^\d{4,15}$/|',
            'name' => 'required_without_all:address,status,orderProducts|string|max:20',
            'address' => 'required_without_all:name,status,orderProducts|string|max:255',
            'status' => 'required_without_all:name,address,orderProducts|string|max:20',
            'orderProducts' => 'nullable|array|min:1',
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
            'orderProducts.*.type' => new Enum(ProductType::class),
            'orderProducts.*.description' => 'string|max:255',
            'orderProducts.*.price' => 'integer',
        ];
    }

    public function messages(): array
    {
        return [
            'orderId.required' => 'An order id is required',
            'phone.string' => 'A phone must be a string',
            'phone.regex' => 'A phone must match format',
            'name.string' => 'An order name must be unique',
            'name.max' => 'An order name length must be less than :max characters',
            'address.string' => 'An order address must be a string',
            'address.max' => 'An order address length must be less than :max characters',
            'status.string' => 'An order status must be a string',
            'status.max' => 'An order status length must be less than :max characters',
            'orderProducts.array' => 'orderProducts must be an array',
            'orderProducts.min' => 'orderProducts must be at least :min',
            'orderProducts.*.quantity' => 'orderProducts.quantity must be an integer',
            'orderProducts.*.title' => 'orderProducts.title must be a string',
            'orderProducts.*.title.max' => 'orderProducts.title must be less than 50 characters long',
            'orderProducts.*.description.string' => 'orderProducts.description must be a string',
            'orderProducts.*.description.max' => 'orderProducts.description must be less than 50 characters long',
            'orderProducts.*.type.in_enum' => 'orderProducts.type must be between ' . implode(', ', ProductType::getTypes()),
            'orderProducts.*.price.integer' => 'orderProducts.price must be an integer',
            '*.required_without_all' => 'At least one field is required',
        ];
    }
}
