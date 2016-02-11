<?php
namespace Birdmin\Support;


use Birdmin\Contracts\Sluggable;
use Birdmin\Core\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Birdmin\Input;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ModelBlueprint {

    /**
     * The array of blueprints.
     * @var array
     */
    static $blueprints = [];

    /**
     * Uses timestamps.
     * @var bool
     */
    public $timestamps = true;

    /**
     * Uses Soft Deletes.
     * @var bool
     */
    public $softDeletes = true;

    /**
     * The table name.
     * @var string
     */
    public $table;

    /**
     * The icon class name.
     * @var string
     */
    public $icon = "file-empty";

    /**
     * Permissions available for the model.
     * These are the defaults.
     * @var array
     */
    public $permissions = ['view','create','edit','delete'];

    /**
     * The field that identifies the title.
     * @var string
     */
    public $title = "id";

    /**
     * The model class.
     * @var string
     */
    protected $class;

    /**
     * If replacing with a new blueprint, this flag will be checked.
     * @var bool
     */
    protected $override = false;

    /**
     * Collection of fields.
     * @var Collection
     */
    protected $fields = [];

    /**
     * Attached module components.
     * @var array
     */
    protected $modules = [];

    /**
     * The model labels.
     * @var Collection
     */
    protected $labels;

    /**
     * A reference to this models index table.
     * @var IndexTableBlueprint
     */
    protected $indexTable;


    /**
     * Named constructor.
     * @param $modelClass string
     * @return static
     */
    public static function create($modelClass, $table=null)
    {
        return new static($modelClass, $table);
    }

    /**
     * Return a blueprint from the static array.
     * @param $modelClass string
     * @return null|ModelBlueprint
     */
    public static function get($modelClass)
    {
        return ModelBlueprint::exists($modelClass) ? ModelBlueprint::$blueprints[$modelClass] : null;
    }

    /**
     * Check if a blueprint exists.
     * @param $modelClass string
     * @return bool
     */
    public static function exists($modelClass)
    {
        return isset(ModelBlueprint::$blueprints[$modelClass]);
    }

    /**
     * ModelBlueprint constructor.
     * @param $modelClass string
     */
    public function __construct($modelClass,$table=null)
    {
        $this->fields = collect([]);
        $this->labels = collect([]);

        $this->class = $modelClass;
        $this->table($table);

        $this->typicalSetup();

        $this->indexTable = new IndexTableBlueprint($this);

        if (static::exists($modelClass)) {
            $this->override = true;
        }
        static::$blueprints[$modelClass] = $this;
    }

    /**
     * A shorthand way to add fields and inputs.
     * @param $name string
     * @param $arguments array
     * @return $this
     */
    public function __call($name,$arguments)
    {
        if (! Str::startsWith($name,"_")) {
            return null;
        }
        $fieldName = ltrim($name,"_");
        list ($label,$fieldType,$inputType) = $arguments;
        $type = is_array($fieldType) ? array_shift($fieldType) : $fieldType;
        $args = is_array($fieldType) ? $fieldType : null;

        // Just return the field object. ie. $this->_fieldName()
        if (count($arguments) == 0) {
            return $this->field($fieldName);
        }

        // Create a new field.
        $this->field($fieldName, $type, $args);

        if ($inputType) {
            $this->fields->get($fieldName)->input($label, $inputType, $this->fields->count());
        }
        return $this;
    }

    /**
     * Get the index table object.
     * @return IndexTableBlueprint
     */
    public function indexTable()
    {
        return $this->indexTable;
    }

    /**
     * @param $name
     * @param null $type
     * @param null $args
     * @return $this|mixed
     */
    public function field($name, $type=null, $args=null)
    {
        if (func_num_args() == 1) {
            return $this->fields->get($name);
        }
        $this->fields[$name] = new FieldBlueprint($name, $type, $args, $this);

        return $this;
    }

    /**
     * Set multiple fields.
     * @param array $array
     * @return $this
     */
    public function fields($array=[])
    {
        $i=0;
        foreach($array as $name=>$args)
        {
            $type    = is_array($args) ? $args[0] : $args;
            $options = is_array($args) && isset($args[1]) ? $args[1] : null;

            $this->field($name, $type, $options);

            // By default, make the first field the title.
            if ($i==0) {
                $this->title = $this->field($name);
            }
            $i++;
        }
        return $this;
    }

    /**
     * Set the table name.
     * @param $name string
     * @return $this
     */
    public function table($name)
    {
        if (! $name) return $this;
        $this->table = $name;

        if ($this->labels->isEmpty()) {
            $this->guessLabels($name);
        }
        return $this;
    }

    /**
     * Set the icon name.
     * @param $name string
     * @return $this
     */
    public function icon($name)
    {
        $this->icon = $name;
        return $this;
    }

    /**
     * Set the title field, or get the title field.
     * @param $field string
     * @return $this|FieldBlueprint
     */
    public function title($field)
    {
        if (func_num_args() == 0) return $this->title;
        $this->title = $this->field($field);
        return $this;
    }

    /**
     * Set a label name, or get a label by key.
     * @param $name string
     * @param $value
     * @return $this|string
     */
    public function label($name,$value=null)
    {
        if (func_num_args() == 1) return $this->labels[$name];
        $this->labels[$name] = $value;
        return $this;
    }

    /**
     * Set all the labels, or get the label collection.
     * @param $array
     * @return $this|Collection
     */
    public function labels($array=null)
    {
        if (func_num_args() == 0) return $this->labels;
        $this->labels->merge($array);
        return $this;
    }

    /**
     * Set the timestamps attribute.
     * @param bool $bool
     * @return $this
     */
    public function timestamps($bool=true)
    {
        $this->timestamps = $bool;
        return $this;
    }

    /**
     * Set the timestamps attribute.
     * @param bool $bool
     * @return $this
     */
    public function softDeletes($bool=true)
    {
        $this->softDeletes = $bool;
        return $this;
    }

    public function fillable()
    {
        return $this->fillFieldsAttribute(func_get_args(), 'fillable');
    }

    public function guarded()
    {
        return $this->fillFieldsAttribute(func_get_args(), 'guarded');
    }

    public function unique()
    {
        return $this->fillFieldsAttribute(func_get_args(), 'unique');
    }

    public function required()
    {
        return $this->fillFieldsAttribute(func_get_args(), 'required');
    }

    public function in_table()
    {
        return $this->fillFieldsAttribute(func_get_args(), 'in_table');
    }
    public function searchable()
    {
        return $this->fillFieldsAttribute(func_get_args(), 'searchable');
    }

    /**
     * Add a component class.
     * @param $componentClass string
     * @param array $args
     * @return $this
     */
    public function module($componentClass, $args=[])
    {
        $this->modules[] = [$componentClass,$args];
        return $this;
    }
    /**
     * Changes the given attribute name with corresponding value.
     * @param $fields array
     * @param $method string
     * @param bool $bool
     * @return $this|Collection
     */
    protected function fillFieldsAttribute($fields, $method, $bool=true)
    {
        if (count($fields) === 0 ) {
            return $this->fields->filter(function($field) use($method) {
                return $field->$method === true;
            })->map(function($field){
                return $field->getName();
            })->values();
        }
        // Mark all fields.
        if (count($fields) === 1 && $fields[0] === "*") {
            $fields = $this->fields->filter(function($field) {
                return $field->isLocked() === false;
            })->map(function($field){
                return $field->getName();
            })->values();
        }
        foreach ($fields as $field)
        {
            $this->field($field)->$method($bool);
        }
        return $this;
    }

    /**
     * Set the permissions array, or return the array.
     * @return $this|array
     */
    public function permissions()
    {
        if (func_num_args() == 0) return $this->permissions;
        $this->permissions = func_get_args();
        return $this;
    }

    /**
     * Create an input reference.
     * @param string $field
     * @param string $label
     * @param string $type
     * @param int $priority
     * @return $this
     */
    public function input($field, $label, $type, $priority=0)
    {
        try {
            $this->field($field)->input($label,$type,$priority);

        } catch(\Exception $e) {
            // Field not defined
        }

        return $this;
    }

    /**
     * Get the model class.
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Define multiple inputs.
     * @param array $array
     * @return $this
     */
    public function inputs($array=[])
    {
        foreach ($array as $name=>$args)
        {
            $label = array_get($args,0);
            $type  = array_get($args,1);
            $pri   = array_get($args,2,0);

            $this->input($name, $label, $type, $pri);
        }
        return $this;
    }

    /**
     * Return an array of inputs.
     * @return array
     */
    public function getInputsArray()
    {
        $inputs = $this->fields->filter(function($field){
            return $field->input;
        });
        return $inputs->map(function($field) {
            return $field->toInputArray();
        });
    }

    /**
     * Take a guess at what the basic labels will be, based on the given name.
     * @return $this
     */
    public function guessLabels($name=null)
    {
        return $this->labels = $this->labels->merge([
            'singular' => str_replace("_"," ",Str::singular($name)),
            'plural' => str_replace("_"," ",Str::plural($name)),
            'navigation' => Str::ucfirst($name),
            'slug' => Str::slug(Str::plural($name))
        ]);
    }

    /**
     * Adds typical fields based on the class constracts.
     * @return void
     */
    protected function typicalSetup()
    {
        $class = $this->class;
        $static = new $class;

        if ($static instanceof Model) {
            $this->field('id', FieldBlueprint::PRIMARY);
            $this->field('uid', FieldBlueprint::UID);
        }
    }

    /**
     * Mass-assign the field input options.
     * @param $array
     * @return $this
     */
    public function inputOptions($array)
    {
        foreach ($array as $field=>$options)
        {
            $this->field($field)->options($options);
        }
        return $this;
    }

    /**
     * Create the table schema for all the columns.
     * @return void
     */
    public function createSchema()
    {
        Schema::create($this->table, function(Blueprint $table) {
            foreach ($this->fields as $field)
            {
                $field->schema($table);
            }
            if ($this->timestamps) $table->timestamps();
            if ($this->softDeletes) $table->softDeletes();
        });
    }

    /**
     * Drop the table schema.
     * @return void
     */
    public function dropSchema()
    {
        Schema::drop($this->table);
    }
}