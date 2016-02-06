<?php

namespace Birdmin\Providers;

use Illuminate\Support\ServiceProvider;
use Birdmin\Core\Extender;

class ExtensionServiceProvider extends ServiceProvider {

    protected $modules;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Extender $extender)
    {
        $this->app->call([$extender,'boot']);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Extender::class, function($app)
        {
            $extender = new Extender($app);
            $modules = config('app.modules');

            foreach ($modules as $module) {
                $extender->register($module);
            }
            return $extender;
        });
    }
}
