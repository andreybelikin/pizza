<?php

namespace App\Http\Requests\Order;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class OrderAddRequest extends FormRequest
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
            'userId' => $this->route('userId'),
        ]);
        $rules = [
            'userId' => 'required|integer',
            'phone' => 'required_without_all:name,address,orderProducts|string|regex:/^\d{4,15}$/',
            'name' => 'required_without_all:address,phone,orderProducts|string|max:20',
            'address' => 'required_without_all:name,phone,orderProducts|string|max:100',
        ];

        if (auth()->user()->isAdmin()) {
            $adminRules = [
                'status' => ['required_without_all:name,phone,address', new Enum(OrderStatus::class)],
                'orderProducts' => 'required|array|min:1',
                'orderProducts.*.id' => 'required_with:orderProducts|integer|exists:products,id',
                'orderProducts.*.quantity' => 'required_with:orderProducts|integer|min:1',
            ];
            $rules = [...$rules, ...$adminRules];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'userId.required' => 'A userId is required',
            'phone.required' => 'A phone is required',
            'phone.regex' => 'A phone should be a valid phone number',
            'name.required' => 'A name is required',
            'name.max' => 'A name length must not to be more than :max',
            'address.required' => 'A address is required',
            'address.max' => 'A address length must not to be more than :max',
        ];
    }
}
