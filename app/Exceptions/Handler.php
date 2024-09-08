<?php

namespace App\Exceptions;

use App\Dto\Response\InternalErrorResponseDto;
use App\Dto\Response\RequestValidation\FailedValidationResponseDto;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        $this->renderable(function (ValidationException $exception) {
            $responseDto = new FailedValidationResponseDto($exception->validator->errors()->toArray());

            return response()->json($responseDto->toArray(), $responseDto::STATUS);
        });

        $this->renderable(function (AuthorizationException $exception) {
            return $exception->response();
        });

        $this->renderable(function (Exception $exception) {
            dd($exception);
            $responseDto = new InternalErrorResponseDto();

            return response()->json($responseDto->toArray(), $responseDto::STATUS);
        });
    }
}
