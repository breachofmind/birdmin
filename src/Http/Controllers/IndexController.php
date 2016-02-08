<?php

namespace Birdmin\Http\Controllers;

use Birdmin\Core\Template;
use Birdmin\Http\Requests;
use Birdmin\Core\Controller;
use Birdmin\Components\Button;

class IndexController extends Controller
{
    public function __construct(Template $template)
    {
        parent::__construct($template, ['auth']);
    }


    public function index()
    {
        $this->setActions([
            Button::create()->link('home')->active(),
        ]);

        return $this->birdmin('cms::home');
    }

}
