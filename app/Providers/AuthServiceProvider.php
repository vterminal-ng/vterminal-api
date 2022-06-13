<?php

namespace App\Providers;

use App\Models\MerchantDetail;
use App\Models\UserDetail;
use App\Policies\MerchantDetailPolicy;
use App\Policies\UserDetailPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        UserDetail::class => UserDetailPolicy::class,
        MerchantDetail::class => MerchantDetailPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
