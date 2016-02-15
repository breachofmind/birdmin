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

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var \simple_html_dom_node
     */
    protected $node;

    /**
     * Contents of the node.
     * @var string
     */
    protected $contents;

    /**
     * TestHtmlComponent constructor.
     * @param Model $model
     * @param \simple_html_dom_node $node
     */
    public function __construct(Model $model, \simple_html_dom_node $node)
    {
        parent::__construct();

        $this->setup($model,$node);
    }

    /**
     * Set up the component.
     * @param Model $model
     * @param \simple_html_dom_node $node
     * @return void
     */
    public function setup(Model $model, \simple_html_dom_node $node)
    {
        $this->model = $model;
        $this->node = $node;

        $this->contents = $node->innertext;
    }

    public function toArray()
    {
        return $this->compact('model','contents');
    }

}