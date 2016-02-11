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
    public function boot()
    {
        $this->app->call([$this,'boot_environment']);

        $this->app->call([$this,'boot_views']);

        $this->app->call([$this,'boot_models']);

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
     * Setup the views and template.
     * @param Template $template
     * @param Extender $extender
     * @param Request $request
     */
    public function boot_views(Template $template, Extender $extender, Request $request)
    {
        $this->loadViewsFrom( base_path('cms/assets/views'), 'cms' );

        View::share('template', $template);
        View::share('modules',  $extender);
        View::share('request',  $request);

        // Check for development environment and inject services.
        $livereload = config('view.livereload');

        // Use livereload on local or development?
        if (env('APP_ENV') !== Application::ENV_PROD && $livereload)
        {
            $url = $livereload === true ? "localhost" : $livereload;
            $template->script('livereload', "http://$url:35729/livereload.js");
        }
    }

    /**
     * Install the model blueprints.
     * @return void
     */
    public function boot_models(Extender $extender)
    {
        FieldBlueprint::boot();

        foreach ($extender->getModules() as $class=>$module)
        {
            $name = "{$module->name}.config.php";

            if (file_exists(base_path("cms/conf/$name"))) {
                include base_path("cms/conf/$name");
            }
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
