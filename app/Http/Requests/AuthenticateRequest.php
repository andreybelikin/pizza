<?php

namespace App\Http\Requests;

use App\Dto\Response\Validation\FailedValidationResponseDto;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthenticateRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'A user email is required.',
            'email.email' => 'A user email must be a valid email address.',
            'password.required' => 'A user password is required.',
            'password.max' => 'A password must not be longer than 50 characters.',
        ];
    }

    protected function failedValidation(Validator $validator): JsonResponse
    {
        throw new ValidationException($validator);
    }
}
