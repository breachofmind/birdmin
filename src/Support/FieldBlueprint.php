<?php
namespace Birdmin\Support;


use Illuminate\Database\Schema\Blueprint;

class FieldBlueprint
{
    /**
     * Types of fields.
     */
    const PRIMARY   = 1;
    const UID       = 2;
    const TEXT      = 3;
    const STRING    = 4;
    const REFERENCE = 5;
    const STATUS    = 6;
    const SLUG      = 7;
    const INTEGER   = 8;


    /**
     * The name of the field (column name)
     * @var string
     */
    protected $name;


    public $fillable = false;
    public $guarded = false;
    public $unique = false;
    public $required = false;
    public $in_table = false;

    public $input = null;
    public $description = null;
    public $options = null;

    private $booleans = ['fillable','guarded','unique','required','in_table'];

    public static $fieldSetup = [];

    /**
     * Set up the callback functions for field types.
     * @return void
     */
    public static function boot()
    {
        static::$fieldSetup[static::PRIMARY] = function(Blueprint $table, FieldBlueprint $field) {
            $table->increments($field->name);
        };
        static::$fieldSetup[static::UID] = function(Blueprint $table, FieldBlueprint $field) {
            $table->string($field->name, 32);
        };
        static::$fieldSetup[static::TEXT] = function(Blueprint $table, FieldBlueprint $field) {
            $table->text($field->name);
        };
        static::$fieldSetup[static::STRING] = function(Blueprint $table, FieldBlueprint $field) {
            $table->string($field->name, $field->arg(0,500));
        };
        static::$fieldSetup[static::REFERENCE] = function(Blueprint $table, FieldBlueprint $field) {
            $table->integer($field->name)->unsigned()->references($field->arg(0))->on($field->arg(1));
        };
        static::$fieldSetup[static::STATUS] = function(Blueprint $table, FieldBlueprint $field) {
            $table->string($field->name,30)->default('draft');
        };
        static::$fieldSetup[static::SLUG] = function(Blueprint $table, FieldBlueprint $field) {
            $table->string($field->name,200)->index();
        };
        static::$fieldSetup[static::INTEGER] = function(Blueprint $table, FieldBlueprint $field) {
            $table->integer($field->name);
        };
    }

    public function __construct($name,$type,$args)
    {
        if (empty(FieldBlueprint::$fieldSetup)) {
            FieldBlueprint::boot();
        }
        $this->name = $name;
        $this->type = $type;
        $this->args = $args;
    }

    /**
     * Setup the field type.
     * @return void
     */
    protected function setType()
    {
        $callbacks = static::$fieldSetup;
        $this->setup = array_key_exists($this->type, $callbacks) ? $callbacks[$this->type] : null;
    }

    /**
     * Calls magic boolean properties.
     * @param $name string
     * @param $arguments array
     * @return $this|null
     */
    public function __call($name, $arguments)
    {
        if (in_array($name,$this->booleans)) {
            $this->$name = is_bool($arguments[0]) ? $arguments[0] : true;
            return $this;
        }
        return null;
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
     * Return the field name.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Create a reference to an input field.
     * @param string $type
     * @param null $description string
     * @param null|array $options
     * @return $this
     */
    public function input($type, $description=null, $options=null)
    {
        $this->input = $type;
        $this->description = $description;
        $this->options = $options;

        return $this;
    }

    /**
     * Return a field argument.
     * @param $index int|string
     * @param mixed $default
     * @return mixed
     */
    public function arg($index, $default=null)
    {
        if (! $this->args || !isset($this->args[$index])) return $default;

        return $this->args[$index];
    }

    /**
     * Return an option.
     * @param $index int|string
     * @param mixed $default
     * @return null
     */
    public function option($index, $default=null)
    {
        if (! $this->options || !isset($this->options[$index])) return $default;

        return $this->options[$index];
    }
}