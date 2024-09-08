<?php

namespace App\Exceptions\Token;

use App\Dto\Response\HttpMiddleware\TokenExceptionResponseDto;
use Illuminate\Http\JsonResponse;

class TokenException extends \Exception
{
    public function __construct($message, private readonly ?int $status = null)
    {
        parent::__construct($message, $status);
    }

    public function getResponse(): JsonResponse
    {
        $responseDto = new TokenExceptionResponseDto($this->message);

        return response()->json($responseDto->toArray(), $this->status ?? $responseDto::STATUS);
    }
}
