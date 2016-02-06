<?php
namespace Birdmin\Core;

use Illuminate\Routing\Router;

class Extender {

    /**
     * Extender instance.
     * @var Extender
     */
    protected static $instance;

    /**
     * Application instance.
     * @var Application
     */
    protected $app;

    /**
     * The CMS URI namespace.
     * @var mixed
     */
    protected $cms_uri;

    /**
     * The CMS domain name.
     * @var mixed
     */
    protected $cms_domain;

    /**
     * Loaded Module classes.
     * @var array
     */
    protected $modules = [];

    /**
     * Array of loaded model class names.
     * @var array
     */
    protected $models = [];

    /**
     * Configuration settings for each route group.
     * @var array [config=>settings]
     */
    protected $route_config = [];

    /**
     * Routes defined by the modules.
     * @var array [config=>callables]
     */
    protected $routes = [];


    /**
     * Extender constructor.
     * @param Application $app
     */
    public function __construct (Application $app)
    {
        $this->app = $app;

        $this->cms_uri    = config('app.cms_uri');
        $this->cms_domain = config('app.cms_domain');

        $this->configure_route('cms', [
            'domain'    => $this->cms_domain,
            'namespace' => 'Birdmin\Http\Controllers',
            'prefix'    => $this->cms_uri
        ]);

        $this->configure_route('api', [
            'domain'    => $this->cms_domain,
            'namespace' => 'Birdmin\Http\Controllers',
            'prefix'    => "api/v1"
        ]);
    }

    /**
     * Setup the defined modules.
     * @return void
     */
    public function boot()
    {
        foreach($this->modules as $class=>$module) {
            $module->boot();
            $this->models = array_merge($module->models, $this->models);
        }
        // Map the routes.
        $this->app->call([$this,'map']);
    }

    /**
     * Create a new route configuration.
     * @param $name string handle
     * @param array $args for Router::group
     * @return $this
     */
    public function configure_route($name, array $args=[])
    {
        $this->route_config[$name] = $args;
        $this->routes[$name] = [];
        return $this;
    }

    /**
     * Set a new route.
     * @param $config string route config handle
     * @param $callable callable for Route::group
     * @return $this
     */
    public function route($config, $callable)
    {
        $this->routes[$config][] = $callable;
        return $this;
    }

    /**
     * Create a new route group in the application.
     * @param $router Router
     * @param $routes array of callable
     * @param $settings array for Route::group
     * @return $this
     */
    public function map (Router $router)
    {
        foreach ($this->route_config as $config=>$settings) {
            $routes = $this->routes[$config];
            foreach ($routes as $callable) {
                $router->group($settings,$callable);
            }
        }
        return $this;
    }

    /**
     * Register a module with the extender.
     * @param $class string module name
     * @throws \Exception
     */
    public function register ($class)
    {
        if (!class_exists($class) || !is_subclass_of($class, 'Birdmin\Core\Module')) {
            throw new \Exception("Class '$class' does not exist or is not a Module class");
        }
        $this->modules[$class] = new $class($this, $this->app);
    }

    /**
     * Return the array of loaded model classes.
     * @return array
     */
    public function getModels()
    {
        return $this->models;
    }


    /**
     * Return the defined navigation for each module.
     * They are loaded in order of the config in app.php.
     * @return array of ButtonGroupComponent
     */
    public function getNavigation()
    {
        $components = [];
        foreach ($this->modules as $class => $module) {
            if (!empty($module->navigation)) {
                $components[$class] = $module->navigation;
            }
        }
        return $components;
    }


    /**
     * Return the instance of this object.
     * @return mixed
     */
    public function getInstance()
    {
        return static::$instance;
    }

}