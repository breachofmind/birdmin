<?php

namespace Birdmin\Providers;

use Birdmin\Core\Application;
use Birdmin\Core\Extender;
use Birdmin\Core\Model;
use Birdmin\Support\ModelBluePrint;
use Illuminate\Support\Facades\Blade;
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
    public function boot_environment (Request $request)
    {
        $request->context = Application::context();
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

        // Allow component tags.
        Blade::directive('component', function($expression)
        {
            $args = explode(",",trim($expression,"()"));
            $componentClass = trim(array_shift($args));
            $arguments = "[".implode(",",$args)."]";
            return "<?php echo $componentClass::create($arguments)->render(); ?>";
        });

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

        $this->app->singleton('Parsedown', function($app) {
            return new \Parsedown();
        });

//        $files = glob(birdmin_path('conf/*.yaml'));
//        foreach ($files as $file) {
//            $config = decode_model_yaml($file);
//            Model::$config[$config['model']] = $config;
//        }
    }

}
