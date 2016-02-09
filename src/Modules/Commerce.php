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
        'Birdmin\ProductBundle'
    ];

    public function setup ()
    {
        $this->basicSetup();
    }
}