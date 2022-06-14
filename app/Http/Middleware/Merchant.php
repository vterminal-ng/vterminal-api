<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponder;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Merchant
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

        if ($user->role !== 'merchant') return $this->failureResponse("This user is not a merchant account.", Response::HTTP_UNAUTHORIZED);

        return $next($request);
    }
}
