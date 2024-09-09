<?php

namespace App\Exceptions\Resource;

use App\Dto\Response\HttpMiddleware\TokenExceptionResponseDto;
use App\Dto\Response\Resourse\ResourceExceptionResponseDto;
use Illuminate\Http\JsonResponse;

class ResourceException extends \Exception
{
    public function __construct($message, public readonly ?int $status = null)
    {
        parent::__construct($message, $status);
    }

    public function getResponse(): JsonResponse
    {
        $responseDto = new ResourceExceptionResponseDto($this->message, $this->status);

        return response()->json($responseDto->toArray(), $this->status);
    }
}
