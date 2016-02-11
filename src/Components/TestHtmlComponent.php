<?php
namespace Birdmin\Components;

use Birdmin\Contracts\Hierarchical;
use Birdmin\Core\Model;
use Birdmin\Core\Component;
use Birdmin\Contracts\HTMLComponent;

class TestHtmlComponent extends Component implements HTMLComponent
{
    protected $name = "Button";

    protected $view = "cms::components.test";

    protected $model;

    protected $node;

    protected $contents;

    public function __construct(Model $model, \simple_html_dom_node $node)
    {
        $this->parent($model);
        $this->node($node);
    }

    public function node(\simple_html_dom_node $node)
    {
        $this->node = $node;

        $node->tag = "div";
        $node->attr = [];
        $node->id = "ComponentTest";
        $this->contents = $node->innertext;

    }

    public function parent(Model $model)
    {
        $this->model = $model;
    }

    public function toArray()
    {
        return $this->compact('model','contents');
    }

}