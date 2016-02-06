<?php

namespace Birdmin\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Birdmin\Core\Extender;
use Birdmin\Policies\ModelPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     * @var array
     */
    protected $policies = [];

    /**
     * Register any application authentication / authorization services.
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate, Extender $extender)
    {
        foreach ($extender->getModels() as $class) {
            $this->policies[$class] = ModelPolicy::class;
        }
        $this->registerPolicies($gate);

    }
}
