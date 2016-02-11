<?php
namespace Birdmin\Modules;

use Birdmin\Core\Module;
use Illuminate\Routing\Router;

class Geo extends Module {

    public $name = "Geo";

    protected $dependencies = [];

    public $models = [
        'Birdmin\Location',
    ];

    public function setup ()
    {
        $this->basicSetup();
    }
}