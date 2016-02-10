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

    static $blueprints = [];

    /**
     * The model class.
     * @var string
     */
    protected $class;

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
     * Collection of fields.
     * @var Collection
     */
    protected $fields = [];

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
     * Attached module components.
     * @var array
     */
    protected $modules = [];

    /**
     * Uses timestamps.
     * @var bool
     */
    public $timestamps = true;

    /**
     * uses Soft Deletes.
     * @var bool
     */
    public $softDeletes = false;

    /**
     * The model labels.
     * @var array
     */
    protected $labels = [];

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
    public static function create($modelClass)
    {
        return new static($modelClass);
    }

    /**
     * Return a blueprint from the static array.
     * @param $modelClass string
     * @return null|ModelBlueprint
     */
    public static function get($modelClass)
    {
        return isset(ModelBlueprint::$blueprints[$modelClass]) ? ModelBlueprint::$blueprints[$modelClass] : null;
    }

    /**
     * ModelBlueprint constructor.
     * @param $modelClass string
     */
    public function __construct($modelClass)
    {
        $this->fields = collect([]);

        $this->class = $modelClass;

        $this->typicalSetup();

        $this->indexTable = new IndexTableBlueprint($this);

        ModelBlueprint::$blueprints[$modelClass] = $this;
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
        $this->fields[$name] = new FieldBlueprint($name, $type, $args);

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
        $this->table = $name;

        if (empty($this->labels)) {
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
     * Set the title field.
     * @param $field string
     * @return $this
     */
    public function title($field)
    {
        $this->title = $this->field($field);
        return $this;
    }

    /**
     * Set a label name.
     * @param $name string
     * @param $value
     * @return $this
     */
    public function label($name,$value)
    {
        $this->labels[$name] = $value;
        return $this;
    }

    /**
     * Set all the labels.
     * @param $array
     * @return $this
     */
    public function labels($array)
    {
        $this->labels = $array;
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
     * @return $this|array
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
     * Set the permissions array.
     * @return $this
     */
    public function permissions()
    {
        $this->permissions = func_get_args();
        return $this;
    }

    /**
     * Create an input reference.
     * @param string $field
     * @param string $type
     * @param null|string $description
     * @param null|array $options
     * @return $this
     */
    public function input($field, $type, $description=null, $options=null)
    {
        try {
            $this->field($field)
                ->input($type,$description,$options);
        } catch(\Exception $e) {
            // Field not defined
        }

        return $this;
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
            $type = is_array($args) ? $args[0] :  $args;
            $description = is_array($args) ? array_get($args,1) : null;
            $options = is_array($args) ? array_get($args,2) : null;
            $this->input($name, $type, $description, $options);
        }
        return $this;
    }

    /**
     * Take a guess at what the basic labels will be, based on the given name.
     * @return $this
     */
    public function guessLabels($name=null)
    {
        return $this->labels([
            'singular' => str_replace("_"," ",Str::singular($name)),
            'plural' => str_replace("_"," ",Str::plural($name)),
            'navigation' => Str::upper($name),
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
        if ($static instanceof Sluggable) {
            $this->field('slug',FieldBlueprint::SLUG);
        }
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