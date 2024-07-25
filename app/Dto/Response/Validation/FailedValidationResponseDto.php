<?php

namespace App\Dto\Response\Validation;

use Illuminate\Http\Response;

class FailedValidationResponseDto
{
    private const MESSAGE = 'The given data failed to pass validation';
    public const STATUS = Response::HTTP_BAD_REQUEST;
    public function __construct(
        private array $errors
    ) {}

    public function toArray(): array
    {
        return [
            'message' => self::MESSAGE,
            'errors' => $this->errors,
        ];
    }
}
