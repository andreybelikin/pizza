<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
            'name' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'A user with this email already exists.',
            'email.required' => 'A user email is required.',
            'password.required' => 'A user password is required.',
            'password.min' => 'A user password must be at least 8 characters.',
            'password_confirmation.required' => 'A user confirm password is required.',
            'name.required' => 'A user name is required.',
        ];
    }

    protected function failedValidation(Validator $validator): HttpResponseException
    {
        $errors = $validator->errors()->toArray();
        return new HttpResponseException(
            response()->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
