<?php

namespace Birdmin\Collections;

use Birdmin\Core\Model;
use Illuminate\Database\Eloquent\Collection;

class MetaCollection extends Collection
{
    protected $parent;

    /**
     * Index of metadata objects that apply to entire model class.
     * Example: object => Birdmin\Post
     * @var array
     */
    protected $globals = [];

    /**
     * Index of metadata objects that apply to just the parent object.
     * Example: object => <uid>
     * @var array
     */
    protected $properties  = [];

    /**
     * Constructor.
     * @param array $items
     */
    public function __construct($items = []) {
        parent::__construct($items);

        array_map([$this,'add'], $items);
    }

    /**
     * Sort the meta object into it's property index.
     * @param  $item
     * @return $this
     */
    public function add($item)
    {
        $index = "properties";
        if (is_subclass_of($item->object, Model::class)) {
            $index = "globals";
        }
        $this->{$index}[$item->key] = $item;
        return $this;
    }

    /**
     * Set the parent model, for reference.
     * @param Model $model
     * @return $this
     */
    public function setParent(Model $model)
    {
        $this->parent = $model;
        return $this;
    }

    /**
     * Search the collection for the given meta key and return its value.
     * @usage $model->meta()->myField
     * @param $key string
     * @return string|null
     */
    public function __get($key)
    {
        // First, check if its in the properties index.
        // This is so we can override the global setting.
        if ($meta = $this->getMeta($key)) {
            return $meta->value;
        }
        $meta = $this->getGlobal($key);
        return $meta ? $meta->value : null;

    }

    /**
     * Return an object meta value.
     * @param $key string
     * @return null|Meta
     */
    public function getMeta($key)
    {
        return array_key_exists($key, $this->properties) ? $this->properties[$key] : null;
    }

    /**
     * Return a global meta value.
     * @param $key string
     * @return null|Meta
     */
    public function getGlobal($key)
    {
        return array_key_exists($key, $this->globals) ? $this->globals[$key] : null;
    }
}