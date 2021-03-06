<?php
namespace Birdmin\Modules;

use Birdmin\Core\Module;
use Birdmin\Core\Model;
use Birdmin\Media;
use Illuminate\Routing\Router;

class System extends Module {

    public $name = "System";

    protected $dependencies = [];

    public $models = [
        'Birdmin\User',
        'Birdmin\Role',
        'Birdmin\Input',
        'Birdmin\Media'
    ];

    public function setup (Router $router)
    {
        // Need to be loaded first.
        $this->route('cms', function($router) {
            $router->get ('/',       'IndexController@index');
            $router->get ('logout',  'Auth\AuthController@logout');
            $router->get ('login',   'Auth\AuthController@index');
            $router->post('login',   'Auth\AuthController@authenticate');
        });

        // API route to show that it's there.
        $this->route('api', function($router) {
            $router->get    ('/',             'REST\ModelController@index');
            $router->get    ('/{model}',      'REST\ModelController@fetchAll')->middleware(['model']);
            $router->get    ('/{model}/{id}', 'REST\ModelController@fetch')->middleware(['model']);
            $router->post   ('/{model}',      'REST\ModelController@create')->middleware(['auth','model']);
            $router->put    ('/{model}/{id}', 'REST\ModelController@update')->middleware(['auth','model']);
            $router->delete ('/{model}/{id}', 'REST\ModelController@destroy')->middleware(['auth','model']);
        });

        // Media uploading routes and controller.
        $this->route('cms', function($router) {
            $router->post(Media::getLabel('slug').'/upload', ['as'=>'mediaUpload', 'uses'=>'MediaController@upload']);
            $router->get(Media::getLabel('slug').'/list',    ['as'=>'mediaList',   'uses'=> 'MediaController@select'] );
        });

        $this->basicSetup();
    }

}