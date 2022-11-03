<?php

namespace App\Http\Middleware;

use App\Models\User;
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
        $user = User::find(auth()->id());

        if (!$user->hasVerifiedPhone() ||  !$user->userDetail || !$user->hasVerifiedEmail())
            return $this->failureResponse("Kindly complete your profile setup", Response::HTTP_UNAUTHORIZED);

        // Specific to user role type
        switch ($user->role) {
            case 'customer':
                if (!$user->authorizedCard || !$user->hasSetPin() || !$user->bankDetail)
                    return $this->failureResponse("Kindly complete your profile setup", Response::HTTP_UNAUTHORIZED);
                break;
            case 'merchant':
                if (!$user->hasSetPin())
                    return $this->failureResponse("Kindly complete your profile setup", Response::HTTP_UNAUTHORIZED);
                break;

            default:
                # code...
                break;
        }

        return $next($request);
    }
}
