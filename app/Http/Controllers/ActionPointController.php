<?php

namespace App\Http\Controllers;

use App\Constants\RewardAction;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;

class ActionPointController extends Controller
{
    public function totalRewardPoint()
    {
        return UserDetail::where('user_id', auth()->id())->first()->user->rewardPointTotal();
    }

    public function rewardPoint()
    {
        return User::find(auth()->id())->rewardPointFor(RewardAction::ONLINE_PURCHASE);
    }
}
