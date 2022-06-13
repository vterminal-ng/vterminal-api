<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;

/**
 * @group Profile Management
 */
class MeController extends Controller
{
    use ApiResponder;

    public function getMe(Request $request)
    {
        if (auth()->check()) {
            return $this->meEndpointResponse(new UserResource(auth()->user()));
        }

        return $this->meEndpointResponse(null);
    }
}
