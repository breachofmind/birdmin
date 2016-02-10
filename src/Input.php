<?php

namespace Birdmin;

use Birdmin\Core\Model;
use Birdmin\Collections\InputCollection;
use Illuminate\Contracts\Support\Renderable;

class Input extends Model
{
    /**
     * Input types.
     * Corresponds to the name of the view, also.
     */
    const CHECKBOX  = "checkbox";
    const CODE      = "code";
    const COLOR     = "color";
    const DATE      = "date";
    const EMAIL     = "email";
    const FILE      = "file";
    const HASH      = "hash";
    const HTML      = "html";
    const MODEL     = "model";
    const NUMBER    = "number";
    const PASSWORD  = "password";
    const RADIO     = "radio";
    const SELECT    = "select";
    const SLUG      = "slug";
    const NONE      = "static";
    const TEXT      = "text";
    const TEXTAREA  = "textarea";
    const TOGGLE    = "toggle";
    const URL       = "url";

    protected $model;

    protected $fillable = [
        'name',
        'label',
        'object',
        'priority',
        'type',
        'active',
        'in_table',
        'required',
        'unique',
        'options',
        'description',
        'value',
    ];

    public $timestamps = false;

    protected $table = 'inputs';

    /**
     * Return the json_decoded options attributes.
     * @param $value string
     * @return array|boolean
     */
    public function getOptionsAttribute($value)
    {
        return json_decode($value);
    }

    /**
     * Set the options attribute by encoded it as json.
     * @param $value mixed
     * @return string
     */
    public function setOptionsAttribute($value)
    {
        return $this->attributes['options'] = is_string($value) ? $value : json_encode($value);
    }

    /**
     * Get the value attribute, either the default or the assigned model.
     * @param $value string
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        if ($this->model && !empty($this->model->id)) {
            return $this->model->getAttribute($this->name);
        }
        return $value;
    }

    /**
     * Associate an input with a a model.
     * @param Model $model
     * @return Model
     */
    public function setModel (Model $model)
    {
        return $this->model = $model;
    }

    /**
     * Create a new view of this input.
     * @return string
     */
    public function render()
    {
        if ($this->type=='model') {
            $class = $this->getOption('model');
            $this->models = $class::all();
            $this->class = $class;
        }

        return view("cms::inputs.{$this->type}", ['input'=>$this])->render();
    }

    /**
     * Return a collection of inputs based on the object/class name.
     * @param $objectName
     */
    public static function byObject ($objectName)
    {
        return static::where('object', $objectName)
            ->orderBy('priority','asc')
            ->get();
    }

    /**
     * Custom input collection.
     * @param array $models
     * @return InputCollection
     */
    public function newCollection (array $models=[])
    {
        return new InputCollection($models);
    }

    public function isNullable()
    {
        return $this->getOption('nullable');
    }

    /**
     * Check if this input is disabled.
     * @param string $value to check
     * @param boolean|mixed $if_true - what to return if is disabled.
     * @param boolean|mixed $if_false - what to return if not disabled.
     * @return bool
     */
    public function isDisabled($value, $if_true=true, $if_false=false)
    {
        if ($this->type=='model' && $this->model->uid == $value->uid) {
            return $if_true;
        }
        return $this->disabled ? $if_true : $if_false;
    }

    /**
     * Check if this input is the title field for the given model class.
     * @param boolean|mixed $if_true - what to return if is the title.
     * @param boolean|mixed $if_false - what to return if not the title.
     * @return boolean|null
     */
    public function isTitleField ($if_true=true, $if_false=false)
    {
        $class = $this->object;
        if ($this->model) {
            $class = get_class($this->model);
        }
        return $class::getTitleField() == $this->getAttribute('name') ? $if_true : $if_false;
    }

    /**
     * Return an option from the options object (if any)
     * @param $key string
     * @param null|string $if_empty - what to return if no option exists
     * @return null|mixed
     */
    public function getOption ($key, $if_empty=null)
    {
        $this->options;
        if (empty($this->options) || !property_exists($this->options,$key)) {
            return $if_empty;
        }
        return $this->options->$key;
    }

    /**
     * Prepare the 'values' key for use in checkboxes, radios, etc.
     * @return array
     */
    public function getSelectionOptions ()
    {
        $options = $this->getOption('values');
        if (!$options || empty($options)) {
            return array();
        }
        // If the options property is a string, it might be a callable.
        if (is_string($options)) {
            return call_user_func($options, $this);
        }
        $selections = [];
        foreach ($options as $value=>$label) {
            $selections[] = [$value,$label];
        }
        return $selections;
    }

    /**
     * Return the name attribute field.
     * @return string
     */
    public function nameAttribute()
    {
        $multiples = ['checkbox'];
        $baseAttr = $this->name;

        return in_array($this->type,$multiples) ? $baseAttr."[]" : $baseAttr;
    }

    /**
     * Return the placeholder option, if any.
     * @param null $if_empty - default placeholder, optional.
     * @return mixed|null
     */
    public function getPlaceholder($if_empty=null)
    {
        return $this->getOption('placeholder',$if_empty);
    }

    /**
     * Check if the given value matches the input value.
     * @param $value
     * @param bool|true $if_true - what to return if is selected.
     * @param bool|false $if_false - what to return if not selected.
     * @return bool|mixed
     */
    public function isSelected($value, $if_true=true, $if_false=false)
    {
        if (!is_array($this->value)) {
            return $this->value == $value ? $if_true : $if_false;
        }
        return in_array($value,$this->value) ? $if_true : $if_false;
    }

    /**
     * Return the inputs for the given model.
     * Looks for model class and model uid as the 'object' field.
     * @param $model Model
     * @return mixed
     */
    public static function retrieve ($model)
    {
        if (!empty($model->inputs)) {
            return $model->inputs;
        }
        $inputs = static::where('object', $model->getAttribute('uid'))
            ->orWhere('object', get_class($model))
            ->orderBy('priority', 'asc')
            ->get();

        $inputs->setParent($model);

        return $inputs;
    }
}
