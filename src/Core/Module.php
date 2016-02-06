<?php
namespace Birdmin\Core;

use Birdmin\Components\ButtonComponent;
use Birdmin\Contracts\Hierarchical;
use Illuminate\Container\Container;
use Birdmin\Components\ButtonGroupComponent;

class Module {

    /**
     * Array of module classes that this module depends on.
     * @var array
     */
    protected $dependencies = [];

    /**
     * Application container instance.
     * @var Container
     */
    protected $app;

    /**
     * The Extension management object.
     * @var Extender
     */
    protected $extender;

    /**
     * Main navigation links.
     * @var ButtonGroupComponent
     */
    public $navigation;

    public $models = [];

    /**
     * Constructor.
     * @param Extender $extender
     * @param Application $app
     */
    public function __construct(Extender $extender, Application $app)
    {
        $this->checkDependencies();

        $this->extender = $extender;
        $this->app = $app;

        $this->navigation = ButtonGroupComponent::create()
            ->classes('navigation-list')
            ->element('ul');
    }

    /**
     * Boot method.
     * @return void
     */
    public function boot()
    {
        $this->app->call([$this,'setup']);
    }

    /**
     * Use a model class and set default navigation structure.
     * @param string $class
     * @return $this
     */
    public function navigation ($class)
    {
        ButtonComponent::create()
            ->parent($class)
            ->link('navigation')
            ->setView('cms::components.navigation')
            ->addTo($this->navigation);

        return $this;
    }


    /**
     * Add standard CMS routes for the given model class.
     * @param $class string
     * @return $this
     */
    public function route_cms ($class)
    {
        Model::$map[$class::plural()] = $class;

        $this->extender->route('cms', function($router) use ($class)
        {
            $static = new $class;
            $p = $class::plural();
            $s = $class::singular();
            $router->model("{$s}_id", $class);

            // These routes define the basic CRUD structure.
            $router->get ("$p",                 $this->determineController($class, 'index'));  //models
            $router->get ("$p/create",          $this->determineController($class, 'create')); //models/create
            $router->post("$p/create",          $this->determineController($class, 'store'));  //models/create
            $router->get ("$p/edit/{{$s}_id}",  $this->determineController($class, 'edit'));   //models/edit/id
            $router->post("$p/edit/{{$s}_id}",  $this->determineController($class, 'update')); //models/edit/id
            $router->post("$p/destroy/{$s}_id", $this->determineController($class, 'destroy'));//models/destroy/id

            // Special views.
            if ($static instanceof Hierarchical) {
                $router->get ("$p/tree",  $this->determineController($class, 'tree'));  //models/tree
            }
        });

        return $this;
    }

    /**
     * Add standard CMS routes for the given model class.
     * @param $class string
     * @return $this
     */
    public function route_api ($class)
    {
        $this->extender->route('api', function($router) use ($class)
        {
            $p = $class::plural();
            $s = $class::singular();

            $router->get("$p",           $this->determineController($class,"getAll", "REST\\")); //models
            $router->get("$p/{{$s}_id}", $this->determineController($class,"get", "REST\\"));
        });

        return $this;
    }


    /**
     * Add a route.
     * @param $config string route config handle
     * @param $callable callable for Route::group
     * @return $this
     */
    public function route($config,$callable)
    {
        $this->extender->route($config, $callable);
        return $this;
    }


    /**
     * Check if this module meets its dependency requirements.
     * @return bool
     * @throws \Exception
     */
    protected function checkDependencies()
    {
        $name = get_called_class();
        foreach ($this->dependencies as $class) {
            if (!class_exists($class) || !in_array($class, config('app.modules'))) {
                throw new \Exception("Dependency '$class' missing for '$name' module");
            }
        }
        return true;
    }

    /**
     * Check if there is a custom controller route for the given class and method.
     * If none, just returns the standard ModelController.
     * @param $class string
     * @param $method string
     * @return string
     */
    protected function determineController($class,$method,$prepend=null)
    {
        $namespace = $this->app->getNamespace()."Http\\Controllers\\".$prepend;
        $controller = $class::singular(true)."Controller";
        $controllerClass = $namespace.$controller;

        if (!class_exists($controllerClass)) {
            return $prepend."ModelController@".$method;
        }

        if (!in_array($method, $this->reflectionGetMethods($controllerClass))) {
            return $prepend."ModelController@".$method;
        }
        return $prepend.$controller."@".$method;
    }

    /**
     * Returns all the method names of a class.
     * @param $className string
     * @return array
     */
    private function reflectionGetMethods($className)
    {
        $static = new \ReflectionClass($className);
        $methods = array_map(function($reflectionMethod){
            return $reflectionMethod->name;
        }, $static->getMethods());
        return $methods;
    }

    /**
     * Provides the basics for getting a module up and running.
     */
    public function basicSetup()
    {
        foreach ($this->models as $class) {
            $this->navigation($class)
                ->route_cms($class)
                ->route_api($class);
        }
    }
}