<?php

namespace App\Exceptions;

use App\Exceptions\Limit\CartLimitException;
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
            return response()->json(
                $exception->validator->errors()->toArray(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        });

        $this->renderable(function (CartLimitException $exception) {
            return response()->json($exception->violations, Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        $this->renderable(function (AccessDeniedHttpException $exception) {
            return response()->json($exception->getMessage() ?: 'Access denied', Response::HTTP_FORBIDDEN);
        });

        $this->renderable(function (NotFoundHttpException $exception) {
            return response()->json('Resource not found', Response::HTTP_NOT_FOUND);
        });

        $this->renderable(function (Exception $exception) {
            return response()->json('Something went wrong. Try again later', Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    }
}
