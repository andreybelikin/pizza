<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class OrdersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    public function rules(): array
    {
        return [
            'userId' => 'nullable|integer',
            'productTitle' => 'nullable|string',
            'minSum' => 'nullable|float',
            'maxSum' => 'nullable|float',
            'status' => ['nullable', new Enum(OrderStatus::class)],
            'createdAt' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function messages(): array
    {
        return [
            'userId.integer' => 'A userId must be integer',
            'productTitle' => 'A title must be string',
            'minSum' => 'A minSum must be float',
            'maxSum' => 'A maxSum must be float',
            'status' => 'A status must fit statuses',
            'createdAt' => 'A createdAt must be correct datetime format',
        ];
    }
}
