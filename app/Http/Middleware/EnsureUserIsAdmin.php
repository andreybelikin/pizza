<?php

namespace App\Http\Middleware;

use App\Exceptions\Resource\ResourceAccessException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()->isAdmin()) {
            throw new ResourceAccessException();
        }

        $request->user()->isAdmin();
        return $next($request);
    }
}
