<?php
namespace Birdmin\Support;


use Birdmin\Contracts\Sluggable;
use Birdmin\Core\Model;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Birdmin\Input;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Sunra\PhpSimple\HtmlDomParser;
use Birdmin\Contracts\HTMLComponent;

class HTMLProcesser implements Renderable
{
    /**
     * The DOM object.
     * @var \simple_html_dom
     */
    protected $dom;

    /**
     * The parsed HTML object.
     * @var \simple_html_dom_node
     */
    protected $html;

    /**
     * References to all components after processing.
     * @var array
     */
    protected $components = [];

    /**
     * Parent Model object.
     * @var Model
     */
    protected $model;

    /**
     * Named constructor.
     * @param $string string html
     * @return static
     */
    static public function parse($string)
    {
        return new static($string);
    }

    /**
     * HTMLProcesser constructor.
     * @param null|string $string
     */
    public function __construct($string=null)
    {

        $this->dom = HtmlDomParser::str_get_html($string);
        $this->html = $this->dom->root;
    }

    /**
     * Set the parent model for use by any components.
     * @param Model $model
     * @return $this
     */
    public function uses(Model $model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Return the HTML string of just the given ID.
     * @param $id string
     * @param $pop boolean - remove the element when done processing?
     * @return string
     * @throws \Exception
     */
    public function getId($id, $pop=true)
    {
        if ($node = $this->html->getElementById($id)) {
            $html = $this->process($node);
            if ($pop) {
                $node->outertext = "";
            }
            return $html;
        }

        return "";
    }

    /**
     * Render to a string.
     * @return string
     */
    public function render()
    {
        return $this->__toString();
    }

    /**
     * Process the component objects inside the HTML DOM.
     * Return the processed string.
     * @param mixed|\simple_html_dom_node $rootNode
     * @return mixed
     * @throws \Exception
     */
    protected function process($rootNode)
    {
        if (! $rootNode instanceof \simple_html_dom_node) {
            return "";
        }
        $this->processComponents($rootNode);

        return $rootNode->parent == null ? $rootNode->innertext : $rootNode->outertext;
    }

    /**
     * Process each <component> element in the node.
     * @param \simple_html_dom_node $rootNode
     * @throws \Exception
     */
    protected function processComponents(\simple_html_dom_node $rootNode)
    {
        foreach((array) $rootNode->find('component') as $node)
        {
            $class = $node->name;

            if (! class_exists($class)) {
                throw new \Exception("Component class '$class' does not exist.");
            }
            if (! has_contract($class, HTMLComponent::class)) {
                throw new \Exception("Component class '$class' does not implement the HTMLComponent contract.");
            }

            $component = $class::create($this->model,$node);

            $this->components[] = $component;

            // Replace the contents of the node with the component view.
            $node->innertext = $component->render();

        }

    }

    /**
     * Echo this object, which processes the html.
     * @return string
     * @throws \Exception
     */
    public function __toString()
    {
        return $this->process($this->html);
    }

}