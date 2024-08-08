<?php

namespace App\Exceptions;

use App\Dto\Response\HttpMiddleware\TokenErrorResponseDto;
use Illuminate\Http\JsonResponse;

class TokenException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public function getResponse(): JsonResponse
    {
        $responseDto = new TokenErrorResponseDto($this->message);

        return response()->json($responseDto->toArray(), $responseDto::STATUS);
    }
}
