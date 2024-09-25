<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UserUpdateRequest extends FormRequest
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
            'name' => 'string|max:50',
            'surname' => 'nullable|string|max:50',
            'phone' => 'regex:/^\d{4,15}$/|unique:users,phone',
            'email' => 'email',
            'default_address' => 'string|max:255',
        ];
    }

    protected function withValidator($validator): void
    {
        $validator->sometimes('email', 'unique:users,email', function ($input) {
            return !User::query()->where('phone', $input->phone)->exists();
        });
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
            'id.required' => 'A user id is required',
            'id.max' => 'A user id is only 36 char long',
            'email.email' => 'A user email must be a valid email address.',
            'email.unique' => 'A user with this email already exists.',
            'phone.regex' => 'A user phone must correspond to international phone number format.',
            'name.max' => 'A user name is only 50 char long.',
            'surname.max' => 'A surname is only 50 char long.',
            'default_address.max' => 'A user default address is only 255 char long.',
        ];
    }

    protected function failedValidation(Validator $validator): JsonResponse
    {
        throw new ValidationException($validator);
    }
}
