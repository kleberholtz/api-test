<?php

namespace App\Http\Middleware;

use App\Models\User as mUsers;
use Closure;
use App\goHoltz\API\Response as API;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class accessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (($params = API::validate([
            'bearer_token' => ['required', 'string', 'regex:/^[a-zA-Z0-9]{40}$/'],
        ], [
            'bearer_token' => $request->bearerToken()
        ], $response)) instanceof JsonResponse) {
            return $params;
        }

        $cacheKey = "user.{$params["bearer_token"]}";
        if (Cache::has($cacheKey)) {
            $user = Cache::get($cacheKey);

            $request->merge(['user' => $user]);
            $request->setUserResolver(function () use ($user) {
                return $user;
            });
            return $next($request);
        }

        $user = mUsers::findByToken($params['bearer_token']);
        if ($user === null) {
            $response->addMessage('Token not found.', $response::DEBUG);
            return API::fail($response, 'Invalid token.', API::HTTP_UNAUTHORIZED);
        }

        $expires_at = now()->parse($user->access_token->expires_at);
        if ($expires_at->isPast()) {
            $response->addMessage('Token expired.', $response::DEBUG);
            return API::fail($response, 'Invalid token.', API::HTTP_UNAUTHORIZED);
        }

        Cache::put($cacheKey, $user, $expires_at);

        $request->merge(['user' => $user]);
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        return $next($request);
    }
}
