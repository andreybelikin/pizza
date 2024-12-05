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
        return [
            'user_id' => 'required|integer',
            'phone' => 'required|string|regex:/^\d{4,15}$/|',
            'name' => 'required|string|max:20',
            'address' => 'required|string|max:100',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => $this->route('userId'),
        ]);
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'A user_id is required',
            'phone.required' => 'A phone is required',
            'phone.regex' => 'A phone should be a valid phone number',
            'name.required' => 'A name is required',
            'name.max' => 'A name length must not to be more than 20',
            'address.required' => 'A address is required',
            'address.max' => 'A address length must not to be more than 100',
        ];
    }
}
