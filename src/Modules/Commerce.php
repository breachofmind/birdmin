<?php
namespace Birdmin\Modules;

use Birdmin\Core\Module;
use Birdmin\Page;
use Illuminate\Routing\Router;

class Commerce extends Module {

    protected $dependencies = [];

    public $models = [
        'Birdmin\Product',
        'Birdmin\ProductVariation',
    ];

    public function setup ()
    {
        $this->basicSetup();
    }
}