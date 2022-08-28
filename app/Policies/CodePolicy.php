<?php

namespace App\Policies;

use App\Models\Code;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CodePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function activateWithSavedCard(User $user, Code $code)
    {
        return $user->authorizedCard->authorization_code === $code->customer->authorizedCard->authorization_code;
    }

    /**
     * Determine whether the user can cancel codes.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function cancel(User $user, Code $code)
    {
        return $user->id === $code->customer_id;
    }
}
