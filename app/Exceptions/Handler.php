<?php

namespace App\Exceptions;

use App\Dto\Response\InternalErrorResponseDto;
use App\Dto\Response\RequestValidation\FailedValidationResponseDto;
use App\Dto\Response\Resourse\CartLimitExceptionResponseDto;
use App\Exceptions\Limit\CartLimitException;
use App\Exceptions\Resource\ResourceException;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        $this->renderable(function (ValidationException $exception) {
            $responseDto = new FailedValidationResponseDto($exception->validator->errors()->toArray());

            return response()->json($responseDto->toArray(), $responseDto::STATUS);
        });

        $this->renderable(function (ResourceException $exception) {
            return $exception->getResponse();
        });

        $this->renderable(function (CartLimitException $exception) {
            $responseDto = new CartLimitExceptionResponseDto($exception->violations);
            return response()->json($responseDto->toArray(), Response::HTTP_BAD_REQUEST);
        });

        $this->renderable(function (AccessDeniedHttpException $exception) {
            return response()->json($exception->getMessage(), Response::HTTP_FORBIDDEN);
        });

        $this->renderable(function (NotFoundHttpException $exception) {
            return response()->json('Resource not found', Response::HTTP_NOT_FOUND);
        });

        $this->renderable(function (Exception $exception) {
            dd($exception);
            $responseDto = new InternalErrorResponseDto();

            return response()->json($responseDto->toArray(), $responseDto::STATUS);
        });
    }
}
