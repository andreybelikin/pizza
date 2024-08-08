<?php

namespace App\Http\Middleware\BeforeRequest;

use App\Exceptions\TokenException;
use App\Services\Middleware\RequestTokenHandler;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            RequestTokenHandler::check($request);

            return $next($request);
        } catch (TokenException $exception) {
            return $exception->getResponse();
        }
    }
}
