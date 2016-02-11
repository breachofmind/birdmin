<?php
namespace Birdmin\Modules;

use Birdmin\Core\Module;
use Birdmin\Page;
use Illuminate\Routing\Router;

class CRM extends Module {

    public $name = "CRM";

    public $models = [
        'Birdmin\Lead',
    ];

    public function setup ()
    {
        $this->basicSetup();
    }
}