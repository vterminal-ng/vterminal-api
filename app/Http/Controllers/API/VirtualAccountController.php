<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\VirtualAccountResource;
use App\Models\User;
use App\Models\VirtualAccount;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;

class VirtualAccountController extends Controller
{
    use ApiResponder;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $virtualAccounts = VirtualAccount::all();

        return $this->successResponse("All users virtual accounts", VirtualAccountResource::collection($virtualAccounts));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user = User::find(auth()->id());

        $virtualAccount = VirtualAccount::with('user')->where("user_id", $user->id)->first();

        return $this->successResponse("All users virtual accounts", new VirtualAccountResource($virtualAccount));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
