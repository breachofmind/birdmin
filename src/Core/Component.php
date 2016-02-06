<?php
namespace Birdmin\Core;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\View;
use Illuminate\Support\Facades\App;
use \JsonSerializable;

/**
 * A component is a self-contained piece of business logic and a view.
 * When configured, it can be added into a template.
 *
 * Class Component
 * @package Birdmin\Core
 */
class Component implements Renderable, Arrayable, Jsonable, JsonSerializable {


    /**
     * The name of the component.
     * @var string
     */
    protected $name;

    /**
     * The data to use with this component (key=>val)
     * @var array
     */
    protected $data = [];

    /**
     * The view object associated with this component.
     * It is compiled with the component $data array.
     * @var View
     */
    protected $view;

    /**
     * Render the object?
     * @var bool
     */
    public $canRender = true;

    /**
     * Attributes for the parent element (typically)
     * @var array
     */
    protected $attributes = [];

    /**
     * Leverages the IoC container for dependency injection.
     * This should give you a little flexibility in building your component.
     * Component constructor.
     */
    public function __construct() {}

    /**
     * Fired just before render().
     * Gives you a chance to store variables into the data array.
     */
    public function prepare()
    {
        $this->data = $this->toArray();
    }

    /**
     * Named constructor.
     * @return mixed
     */
    public static function create()
    {
        return App::make(static::class);
    }

    /**
     * Add an html attribute to the button.
     * @param $name string attribute name
     * @param bool|true $value - true will only display attribute name
     * @return $this
     */
    public function attribute($name,$value=true)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    public function attributes($keyvalue=[])
    {
        foreach ($keyvalue as $key=>$value)
        {
            $this->attribute($key,$value);
        }
        return $this;
    }

    /**
     * Shortcut for adding class names.
     * @param $classes
     * @return Component
     */
    public function classes($classes)
    {
        return $this->attribute('class', $classes);
    }

    /**
     * Return the value of the given attribute.
     * @param $name string
     * @return null|string
     */
    public function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }


    /**
     * Set the view name for this component.
     * @param string $view
     * @return $this
     */
    public function setView ($view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Associate data (key/val) with this component.
     * @param array $data
     * @return $this
     */
    public function with(array $data)
    {
        $this->data = array_merge($data,$this->data);
        return $this;
    }

    /**
     * Render the component.
     * @return View|string
     * @throws \ErrorException
     */
    public function render()
    {
        // Kill switch.
        if (!$this->canRender) {
            return null;
        }

        $this->prepare();

        if (!$this->view) {
            throw new \ErrorException('Component view not set');
        }

        return view($this->view)->with($this->data)->render();
    }

    /**
     * Echoing the object will render the component.
     * @return View|string
     * @throws \ErrorException
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Return the data array.
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Return the data array as a json-encoded string.
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray());
    }

    /**
     * For json_encode.
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}