<?php

namespace App\Traits;

use App\Constants\RewardAction;
use App\Models\ActionPoint;

trait CanRewardPoint
{

    public function actionPoints()
    {
        return $this->morphMany(ActionPoint::class, 'rewardable');
    }

    public function rewardPointFor($action)
    {
        // get the point for the action perfomed
        switch ($action) {
            case RewardAction::POS_PURCHASE:
                $point = 10;
                $this->actionPoints()->create([
                    'performed_action' => $action,
                    'point' => $point,
                ]);
                break;
            case RewardAction::ONLINE_PURCHASE:
                $point = 15;
                $this->actionPoints()->create([
                    'performed_action' => $action,
                    'point' => $point,
                ]);
                break;
            case RewardAction::REFERRAL:
                $point = 50;
                $this->actionPoints()->create([
                    'performed_action' => $action,
                    'point' => $point,
                ]);
                break;
            case RewardAction::PAYMENT:
                $point = 10;
                $this->actionPoints()->create([
                    'performed_action' => $action,
                    'point' => $point,
                ]);
                break;

            default:
                # code...
                break;
        }
        // add to action points table

        // send mail with job
    }

    public function rewardPointsHistory()
    {
        // return reward history
    }

    public function rewardPointTotal()
    {
        // dd('here');
        return $this->actionPoints()->where('is_active', true)->sum('point');
    }
}
