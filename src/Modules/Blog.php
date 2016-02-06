<?php
namespace Birdmin\Modules;

use Birdmin\Core\Module;
use Birdmin\Page;
use Illuminate\Routing\Router;

class Blog extends Module {

    protected $dependencies = ['Birdmin\Modules\Geo'];

    public $models = [
        'Birdmin\Page',
        'Birdmin\Post',
        'Birdmin\Category'
    ];

    public function setup ()
    {
        $this->basicSetup();
    }
}