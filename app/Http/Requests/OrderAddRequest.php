<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderAddRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'userId' => 'required|integer',
            'phone' => 'required|string|regex:/^\d{4,15}$/|',
            'name' => 'required|string|max:20',
            'address' => 'required|string|max:100',
        ];

        if (auth()->user()->isAdmin()) {
            $adminRules = [
                'orderProducts' => 'required|array|min:1',
                'orderProducts.*.id' => 'required|integer|exists:products,id',
                'orderProducts.*.quantity' => 'required_with:orderProducts|integer',
            ];
            $rules = [...$rules, ...$adminRules];
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'userId' => $this->route('userId'),
        ]);
    }

    public function messages(): array
    {
        return [
            'userId.required' => 'A userId is required',
            'phone.required' => 'A phone is required',
            'phone.regex' => 'A phone should be a valid phone number',
            'name.required' => 'A name is required',
            'name.max' => 'A name length must not to be more than 20',
            'address.required' => 'A address is required',
            'address.max' => 'A address length must not to be more than 100',
        ];
    }
}
