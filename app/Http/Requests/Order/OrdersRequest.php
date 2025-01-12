<?php

namespace App\Http\Requests\Order;

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
        return true;
    }

    public function rules(): array
    {
        return [
            'userId' => 'nullable|integer',
            'productTitle' => 'nullable|string',
            'minTotal' => 'nullable|integer',
            'maxTotal' => 'nullable|integer',
            'status' => ['nullable', new Enum(OrderStatus::class)],
            'createdAt' => 'nullable|date_format:d.m.Y',
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
            'minSum' => 'A minSum must be integer',
            'maxSum' => 'A maxSum must be integer',
            'status' => 'A status must fit statuses',
            'createdAt' => 'A createdAt must be correct datetime format',
        ];
    }
}
