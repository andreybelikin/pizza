<?php

namespace App\Http\Middleware\BeforeRequest;

use App\Dto\Response\HttpMiddleware\NotAdminResponseDto;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = auth()->user()->getAuthIdentifier();
        $user = User::query()->find($userId);

        if ($user->isAdmin()) {
            return $next($request);
        } else {
            $responseDto = new NotAdminResponseDto();

            return response()->json($responseDto->toArray(), $responseDto::STATUS);
        }
    }
}
