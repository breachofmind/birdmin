<?php
namespace Birdmin\Support;


use Birdmin\Contracts\RelatedMedia;
use Birdmin\Contracts\Sluggable;
use Birdmin\Core\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Birdmin\Input;
use Illuminate\Support\Facades\Schema;

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
     * Collection of fields.
     * @var Collection
     */
    protected $fields = [];

    /**
     * Permissions available for the model.
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
    protected $labels = [
        'singular'   => 'model',
        'plural'     => 'models',
        'navigation' => 'Model',
    ];

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
     * ModelBlueprint constructor.
     * @param $modelClass string
     */
    public function __construct($modelClass)
    {
        $this->class = $modelClass;

        $this->typicalSetup();

        ModelBlueprint::$blueprints[$modelClass] = $this;
    }

    /**
     * @param $name
     * @param null $type
     * @param null $args
     * @return $this|mixed
     */
    public function field($name, $type=null, $args=null)
    {
        if (empty($this->fields)) $this->fields = collect([]);
        if (func_num_args() == 1) {
            return $this->fields->get($name);
        }
        $this->fields[$name] = new FieldBlueprint($name, $type, $args);
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
            })->map(function($field){ return $field->getName(); })->values();
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
        } catch(\ErrorException $e) {
            // Field not defined
        }

        return $this;
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
     */
    public function createSchema()
    {
        $modelBlueprint = $this;
        $schema = Schema::create($this->table, function(Blueprint $table) {
            foreach ($this->fields as $field)
            {
                $field->schema($table);
            }
            if ($this->timestamps) $table->timestamps();
            if ($this->softDeletes) $table->softDeletes();
        });
        dd($schema);
    }
}