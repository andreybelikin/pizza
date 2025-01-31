<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TokensRequest extends FormRequest
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
            'accessToken' => 'required|string',
            'refreshToken' => 'required|string'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'accessToken' => $this->bearerToken(),
            'refreshToken' => $this->header('x-refresh-token'),
        ]);
    }
}
