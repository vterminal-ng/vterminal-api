<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Traits\ApiResponder;
use App\Models\User;


class ApiKey
{
    use ApiResponder;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!$request->has('api_key')) return $this->failureResponse('API Key is required', Response::HTTP_UNAUTHORIZED);

        $user = User::where('api_key', $request->query('api_key'))->first();

        if(!$user) return $this->failureResponse('Invalid: API Key Not Found', Response::HTTP_UNAUTHORIZED);

        return $next($request);
    }
}
