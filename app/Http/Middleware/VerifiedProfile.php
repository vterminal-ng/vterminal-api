<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponder;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifiedProfile
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
        $user = auth()->user();

        if (!$user->hasVerifiedEmail() || !$user->hasVerifiedPhone() || !$user->userDetail || !$user->bankDetail) return $this->failureResponse("Kindly complete your profile verification before you proceed", Response::HTTP_UNAUTHORIZED);

        return $next($request);
    }
}
