<?php

namespace Birdmin\Collections;

use Birdmin\Input;
use Birdmin\Core\Model;
use Illuminate\Database\Eloquent\Collection;

class InputCollection extends Collection
{
    public $fields = [];

    /**
     * The parent model associated with these inputs.
     * @var Model
     */
    protected $parent;

    /**
     * The model class.
     * @var string
     */
    protected $class;

    /**
     * Constructor.
     * @param array $items
     */
    public function __construct($items = [])
    {
        parent::__construct($items);

        foreach ($this->items as $input) {
            if (!$this->class) $this->class = $input->object;
            $this->fields[$input->name] = $input;
        }
    }

    /**
     * Add an item to the collection.
     * @param  $item
     * @return $this
     */
    public function add($item)
    {
        $this->items[] = $item;
        $this->fields[$item->name] = $item;
        return $this;
    }

    /**
     * Return as list of fields.
     * @return array
     */
    public function getFields()
    {
        return array_keys($this->fields);
    }

    /**
     * Return the input object or property value, given the field name.
     * @param $field string
     * @param null $property string
     * @return mixed
     */
    public function getField($field, $property=null)
    {
        if (!array_key_exists($field, $this->fields)) {
            return null;
        }
        return $property ? $this->fields[$field]->$property : $this->fields[$field];
    }

    /**
     * Return the label for a given field.
     * @param $field string
     * @return bool
     */
    public function getLabel($field)
    {
        return $this->getField($field,'label');
    }

    /**
     * Return a name=>label array.
     * Used for custom validation attributes.
     * @return array
     */
    public function labels()
    {
        $labels = [];
        foreach ($this->items as $input) {
            $labels[$input->name] = $input->label;
        }
        return $labels;
    }

    /**
     * Return the value of a field, if the parent model is given.
     * This is kind of like an alias into the parent model.
     * @param $field
     * @return null
     */
    public function getValue($field)
    {
        return $this->parent ? $this->parent->$field : null;
    }

    /**
     * Set the parent model for the inputs in this collection.
     * @param Model $model
     * @return InputCollection
     */
    public function setParent(Model $model)
    {
        $this->parent = $model;
        $this->each(function(Input $input) {
            $input->setModel($this->parent);
        });
        return $this;
    }

    /**
     * Return an array of rules for the laravel validator.
     * @return array
     */
    public function rules ()
    {
        $rules = array();

        if ($this->isEmpty()) {
            return $rules;
        }

        $class = $this->class;
        $table = $this->parent ? $this->parent->getTable() : with(new $class)->getTable();
        $types = [
            Input::EMAIL     => 'email',
            Input::NUMBER    => 'numeric',
            Input::DATE      => 'date',
            Input::TOGGLE    => 'boolean',
            Input::TEXT      => 'string',
            Input::SLUG      => 'alpha_dash',
            Input::MODEL     => 'integer',
        ];

        foreach ($this->items as $input)
        {
            $rule = array();
            if ($input->required) {
                $rule[] = "required";
            }
            if ($input->unique) {
                // unique:posts,slug,{id to exclude}
                $rule[] = "unique:".$table.",".$input->name.($this->parent ? ",".$this->parent->id : "");
            }
            if (array_key_exists($input->type, $types)) {
                $rule[] = $types[$input->type];
            }
            if (!empty($rule)) {
                $rules[$input->name] = join("|",$rule);
            }
        }

        return $rules;
    }

    /**
     * Render all the inputs in order.
     * @return string
     */
    public function render()
    {
        $html = "";
        foreach($this->items as $input) {
            $html.=$input->render();
        }
        return $html;
    }
}