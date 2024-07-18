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
            'phone' => 'required|phone|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|regex:/^.*(?=.{8,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
            'password_confirmation' => 'required|same:password',
            'name' => 'required',
            'default_address' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'A user with this email already exists.',
            'email.required' => 'A user email is required.',
            'password.required' => 'A user password is required.',
            'password.min' => 'A user password must be at least 8 characters.',
            'password.regex' => 'A user password must meet the following criteria:
                At least 8 characters long
                Contains at least one letter (uppercase or lowercase)
                Contains at least one digit (0-9)
                Contains at least one special character (!, $, #, or %)'
            ,
            'password_confirmation.required' => 'A user confirm password is required.',
            'name.required' => 'A user name is required.',
            'default_address' => 'A user default address is required'
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
