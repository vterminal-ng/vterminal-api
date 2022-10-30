<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'rewardable_id',
        'rewardable_type',
        'performed_action',
        'point',
        'reference',
        'is_active',
    ];

    public function rewardable()
    {
        return $this->morphTo();
    }
}
