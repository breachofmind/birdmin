<?php
namespace Birdmin\Support;


use Birdmin\Input;
use Illuminate\Database\Schema\Blueprint;

class FieldBlueprint
{
    /**
     * Basic Types of fields.
     * @var int
     */
    const PRIMARY   = 1;
    const UID       = 2;
    const TEXT      = 3;
    const STRING    = 4;
    const REFERENCE = 5;
    const STATUS    = 6;
    const SLUG      = 7;
    const INTEGER   = 8;
    const TITLE     = 9;
    const DATE      = 10;
    const TIMESTAMP = 11;


    /**
     * The name of the field (column name)
     * @var string
     */
    protected $name;

    /**
     * The parent model blueprint.
     * @var ModelBlueprint
     */
    protected $blueprint;

    protected $properties = [
        'fillable'    => false,
        'guarded'     => false,
        'unique'      => false,
        'required'    => false,
        'in_table'    => false,
        'searchable'  => false,
        'dates'       => false,
        'hidden'      => false,
    ];

    /**
     * The basic field type.
     * @var int
     */
    public $type;

    /**
     * the input type (optional)
     * @var string
     */
    public $input;

    /**
     * Options for the input.
     * @var array
     */
    public $options;

    /**
     * The input priority.
     * @var int
     */
    public $priority = 0;

    /**
     * The default value for the input.
     * @var mixed
     */
    public $value;

    /**
     * The field label.
     * @var string
     */
    public $label;

    /**
     * The field description.
     * @var string
     */
    public $description;

    /**
     * Arguments for the schema generator.
     * @var array
     */
    public $args;

    /**
     * Fields that should not be fillable.
     * @var array
     */
    public static $lockedFields = [];

    /**
     * Callbacks to fire based on the field type.
     * @var array
     */
    protected static $fieldSetup = [];

    /**
     * Register a new callback for when the schema is created.
     * @param $name string
     * @param \Closure $callable
     */
    public static function registerCallback($name,\Closure $callable)
    {
        static::$fieldSetup[$name] = $callable;
    }

    /**
     * FieldBlueprint constructor.
     * @param $name string
     * @param $type string field type
     * @param $args
     * @param ModelBlueprint|null $parent
     */
    public function __construct($name,$type,$args, ModelBlueprint $parent=null)
    {
        if (empty(FieldBlueprint::$fieldSetup)) {
            FieldBlueprint::boot();
        }
        $this->name = $name;
        $this->type = $type;
        $this->args = $args;

        if ($parent)
        {
            $this->blueprint = $parent;

            // The descriptions are located in localization files.
            // tableName.columnName => description with {plural} labels
            $this->description = trans("{$parent->table()}.$name", $parent->labels()->toArray());
        }
    }

    /**
     * Named constructor.
     * @param $name string
     * @param $type string field type
     * @param $args
     * @param ModelBlueprint|null $parent
     * @return static
     */
    public static function create($name,$type,$args,ModelBlueprint $parent=null)
    {
        return new static($name,$type,$args,$parent);
    }

    /**
     * Set a boolean property.
     * @param $name string
     * @param $bool boolean
     * @return $this
     */
    public function setProperty($name,$bool)
    {
        if (array_key_exists($name,$this->properties) && !$this->isLocked() && is_bool($bool)) {
            $this->properties[$name] = $bool;
        }
        return $this;
    }

    /**
     * Get a property of this field.
     * @param $name string
     * @return null
     */
    public function __get($name)
    {
        if (in_array($name,$this->properties)) {
            return $this->properties[$name];
        }
        return null;
    }

    /**
     * Set a boolean property of this field.
     * @param $name string
     * @param $value boolean
     * @return $this
     */
    public function __set($name, $value)
    {
        return $this->setProperty($name,$value);
    }

    /**
     * Check if this field should be fillable.
     * @return bool
     */
    public function isLocked()
    {
        return in_array($this->type, static::$lockedFields);
    }

    /**
     * Return the field name.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Create a reference to an input model.
     * @param $label string
     * @param $type string
     * @param $priority int
     * @return $this
     */
    public function withInput($label, $type, $priority=0)
    {
        $this->label = $label;
        $this->input = $type;
        $this->priority = $priority;

        return $this;
    }

    /**
     * Create an input object from this field.
     * @return null|false|Input
     */
    public function createInput()
    {
        if (! $this->input) {
            return null;
        }
        if ( Input::where('name', $this->name )
            ->where('object', $this->blueprint->getClass())
            ->exists() ) {
            return false;
        }

        return Input::create($this->toInputArray());

    }

    /**
     * Create a array, for Input::create().
     * @return array
     */
    public function toInputArray()
    {
        return [
            'object'        => $this->blueprint->getClass(),
            'name'          => $this->name,
            'label'         => $this->label,
            'priority'      => $this->priority,
            'description'   => $this->description,
            'type'          => $this->input,
            'options'       => $this->options ? json_encode($this->options) : "",
            'required'      => $this->required ? 1:0,
            'unique'        => $this->unique ? 1:0,
            'in_table'      => $this->in_table ? 1:0,
            'value'         => $this->value?:"",
            'active'        => 1,
        ];
    }


    /**
     * Run the schema function.
     * @param Blueprint $table
     * @return null
     */
    public function schema(Blueprint $table)
    {
        $callbacks = static::$fieldSetup;
        $callback = array_key_exists($this->type, $callbacks) ? $callbacks[$this->type] : null;

        if (! is_callable($callback)) {
            return null;
        }
        return $callback($table,$this);
    }

    /**
     * Access the args array.
     * @param $index
     * @param null $default
     * @return mixed
     */
    public function arg($index,$default=null)
    {
        return array_get($this->args, $index, $default);
    }


    /**
     * Set up the callback functions for field types.
     * @return void
     */
    public static function boot()
    {
        $defaults = [
            static::PRIMARY => function(Blueprint $table, FieldBlueprint $field) {
                $table->increments($field->getName());
            },

            static::UID => function(Blueprint $table, FieldBlueprint $field) {
                $table->string($field->getName(), 32);
            },

            static::TEXT => function(Blueprint $table, FieldBlueprint $field) {
                $table->text($field->getName());
            },

            static::STRING => function(Blueprint $table, FieldBlueprint $field) {
                $table->string($field->getName(), $field->arg(0,500));
            },

            static::REFERENCE => function(Blueprint $table, FieldBlueprint $field) {
                $table->integer($field->getName())->unsigned()->references($field->arg(0))->on($field->arg(1));
            },

            static::STATUS => function(Blueprint $table, FieldBlueprint $field) {
                $table->string($field->getName(),30)->default('draft');
            },

            static::SLUG => function(Blueprint $table, FieldBlueprint $field) {
                $table->string($field->getName(),200)->index();
            },

            static::INTEGER => function(Blueprint $table, FieldBlueprint $field) {
                $table->integer($field->getName());
            },

            static::TITLE => function(Blueprint $table, FieldBlueprint $field) {
                $table->string($field->getName(), 300);
            },

            static::DATE => function(Blueprint $table, FieldBlueprint $field) {
                $table->date($field->getName());
            },

            static::TIMESTAMP => function(Blueprint $table, FieldBlueprint $field) {
                $table->timestamp($field->getName());
            }
        ];

        // Register them all.
        foreach($defaults as $key=>$callable) {
            static::registerCallback($key,$callable);
        }

        // Set some common locked fields.
        static::$lockedFields = [static::PRIMARY, static::UID];
    }
}