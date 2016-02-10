<?php

namespace Birdmin\Providers;

use Birdmin\Core\Application;
use Birdmin\Core\Extender;
use Birdmin\Core\Model;
use Illuminate\Support\ServiceProvider;
use Birdmin\Core\Template;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Birdmin\Support\FieldBlueprint;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot(Template $template, Extender $extender, Request $request)
    {
        $this->app->call([$this,'boot_environment']);

        $this->loadViewsFrom( base_path('cms/assets/views'), 'cms' );

        View::share('template', $template);
        View::share('modules',  $extender);
        View::share('request',  $request);

        // Check for development environment and inject services.
        $livereload = config('view.livereload');
        if (env('APP_ENV') !== Application::ENV_PROD && $livereload) {
            $url = $livereload === true ? "localhost" : $livereload;
            $template->script('livereload', "http://$url:35729/livereload.js");
        }

        FieldBlueprint::boot();
    }

    /**
     * Set up some early environment variables.
     * @param Request $request
     */
    public function boot_environment (Request $request) {
        // Determine the type of request.
        switch ($request->segment(1)) {
            case config('app.cms_uri') :
                $request->context = Application::CXT_CMS;
                break;
            case "api" :
                $request->context = Application::CXT_API;
                break;
            default:
                $request->context = Application::CXT_SITE;
                break;
        }
    }

    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
        include base_path('cms/inc/helpers.php');
        include base_path('cms/inc/formatters.php');

        $this->app->singleton('Birdmin\Core\Template', function($app) {
            return new Template($app);
        });

        // Attach Model configuration to base class.
        $files = glob(birdmin_path('conf/*.yaml'));
        foreach ($files as $file) {
            $config = decode_model_yaml($file);
            Model::$config[$config['model']] = $config;
        }
    }

}
