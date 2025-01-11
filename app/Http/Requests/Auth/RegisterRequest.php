<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'phone' => 'required|regex:/^\d{4,15}$/|unique:users,phone',
            'email' => 'required|email',
            'password' => 'required|string|regex:/^.*(?=.{8,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
            'password_confirmation' => 'required_with:password|same:password',
            'name' => 'required|string|max:50',
            'surname' => 'nullable|string|max:50',
            'default_address' => 'required|string|max:255',
        ];
    }

    protected function withValidator($validator): void
    {
        $validator->sometimes('email', 'unique:users,email', function ($input) {
            return !User::query()->where('phone', $input->phone)->exists();
        });
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'A user with this email already exists.',
            'email.required' => 'A user email is required.',
            'email.email' => 'A user email must be a valid email address.',
            'phone.required' => 'A user phone is required.',
            'phone.regex' => 'A user phone must correspond to international phone number format.',
            'password.required' => 'A user password is required.',
            'password.regex' => 'A user password must meet the following criteria:' . PHP_EOL .
                '    - At least 8 characters long' . PHP_EOL .
                '    - Contains at least one letter (uppercase or lowercase)' . PHP_EOL .
                '    - Contains at least one digit (0-9)' . PHP_EOL .
                '    - Contains at least one special character (!, $, #, or %)'
            ,
            'password_confirmation.required' => 'A user confirm password is required.',
            'name.required' => 'A user name is required.',
            'name.max' => 'A user name is only :max char long.',
            'surname.required' => 'A user surname is required.',
            'surname.max' => 'A surname is only :max char long.',
            'default_address.required' => 'A user default address is required',
            'default_address.max' => 'A user default address is only :max char long.',
        ];
    }

    protected function failedValidation(Validator $validator): JsonResponse
    {
        throw new ValidationException($validator);
    }
}
