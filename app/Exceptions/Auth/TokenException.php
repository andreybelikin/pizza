<?php

namespace App\Exceptions\Auth;

use App\Dto\Response\Middleware\TokenExceptionResponseDto;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TokenException extends \Exception
{
    const STATUS = Response::HTTP_UNAUTHORIZED;

    public function __construct(string $message)
    {
        parent::__construct($message, self::STATUS);
    }

    public function getResponse(): JsonResponse
    {
        $responseDto = new TokenExceptionResponseDto($this->message);

        return response()->json($responseDto->toArray(), $responseDto::STATUS);
    }
}
