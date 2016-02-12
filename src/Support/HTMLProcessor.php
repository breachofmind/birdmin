<?php
namespace Birdmin\Support;

use Birdmin\Core\Model;
use Illuminate\Contracts\Support\Renderable;
use Sunra\PhpSimple\HtmlDomParser;

/**
 * Class HTMLProcessor
 *
 * Breaks up an HTML string into a DOM tree.
 * Can process custom HTML tags.
 * @package Birdmin\Support
 */
class HTMLProcessor implements Renderable
{
    /**
     * Array of tags to process.
     * @var array
     */
    protected static $tags = [];

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
     * Register a new HTML tag.
     * @param $tag
     * @param \Closure $callable
     */
    static public function register($tag, \Closure $callable)
    {
        static::$tags[$tag] = $callable;
    }

    /**
     * HTMLProcessor constructor.
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
        //$this->processComponents($rootNode);
        foreach (array_keys(static::$tags) as $tag)
        {
            $this->processTag($tag, $rootNode);
        }

        return $rootNode->parent == null ? $rootNode->innertext : $rootNode->outertext;
    }

    /**
     * Process a tag.
     * @param $tag string
     * @param \simple_html_dom_node $rootNode
     * @return void
     */
    protected function processTag($tag, \simple_html_dom_node $rootNode)
    {
        $closure = static::$tags[$tag];

        foreach ((array) $rootNode->find($tag) as $node)
        {
            $closure($node, $this);
        }
    }

    /**
     * Get the model object.
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
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