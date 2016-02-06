<?php

namespace Birdmin\Http\Controllers;

use Birdmin\Core\Template;
use Birdmin\Http\Requests;
use Birdmin\Core\Controller;
use Birdmin\Components\ButtonComponent;
use Birdmin\Components\ButtonGroupComponent;

class IndexController extends Controller
{
    public function __construct(Template $template)
    {
        parent::__construct($template, ['auth']);
    }


    public function index()
    {
        $actions = ButtonGroupComponent::build([
            ButtonComponent::create()->link('home')->active(),
        ])->render();

        $this->data = compact('actions');

        return $this->birdmin('cms::home');
    }

}
