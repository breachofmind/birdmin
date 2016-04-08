<?php
namespace Birdmin\Support;


use Birdmin\Contracts\Sluggable;
use Birdmin\Core\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Birdmin\Input;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ModelBluePrint
{
    /**
     * The table name.
     * @var string
     */
    protected $table;

    /**
     * The model class name.
     * @var string
     */
    protected $class;

    /**
     * Array of module classes and options.
     * @var array
     */
    protected $modules = [];

    /**
     * If replacing with a new blueprint, this flag will be checked.
     * @var bool
     */
    protected $overridden = false;

    /**
     * Attributes applicable to the entire model.
     * @var array
     */
    protected $attributes = [
        'public'      => true,
        'timestamps'  => false,
        'softDeletes' => false,
        'no_image'    => '/cms/public/images/no-image.svg',
        'icon'        => 'file-empty',
        'permissions' => ['view','create','edit','delete'],
        'url'         => "{slug}"
    ];

    /**
     * The title field.
     * @var FieldBlueprint
     */
    protected $title;

    /**
     * Boolean values that can be set across fields.
     * @var array
     */
    protected $fieldProperties = [
        'fillable',
        'guarded',
        'unique',
        'required',
        'in_table',
        'searchable',
        'dates',
        'hidden'
    ];

    /**
     * Collection of field objects.
     * @var Collection
     */
    protected $fields;

    /**
     * Collection of labels.
     * @var Collection
     */
    protected $labels;

    /**
     * Array of blueprint objects.
     * @var array
     */
    public static $blueprints = [];

    /**
     * Create a new Blueprint instance.
     * @param $class string
     * @param $table string
     * ModelBluePrintNew constructor.
     */
    public function __construct($class,$table=null)
    {
        $this->class = $class;

        $this->fields = collect();
        $this->labels = collect();

        $this->table($table);

        if (static::exists($class)) {
            $this->overridden = true;
        }

        $this->typicalSetup();

        static::$blueprints[$class] = $this;
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
            $this->_id  ("ID",  FieldBlueprint::PRIMARY, null);
            $this->_uid ("UID", FieldBlueprint::UID,     null);
        }
    }



    /**
     * Named constructor.
     * @param $class string
     * @return static
     */
    public static function create($class, $table=null)
    {
        return new static($class, $table);
    }

    /**
     * Return a blueprint from the static array.
     * @param $class string
     * @return null|ModelBlueprint
     */
    public static function get($class)
    {
        return ModelBlueprint::exists($class) ? ModelBlueprint::$blueprints[$class] : null;
    }

    /**
     * Check if a blueprint exists.
     * @param $class string
     * @return bool
     */
    public static function exists($class)
    {
        return isset(ModelBlueprint::$blueprints[$class]);
    }

    /**
     * Get an attribute.
     * @param $name string
     * @return null|mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name,$this->attributes)) {
            return $this->attributes[$name];
        }
        return null;
    }

    /**
     * A magic method for creating fields and chaining attributes.
     * @param $name string
     * @param $arguments array
     * @return $this
     */
    public function __call($name,$arguments)
    {
        // If an underscore given, we're asking for a field object.
        if (Str::startsWith($name,"_")) {
            $field = ltrim($name,"_");
            if (count($arguments) == 0) {
                return $this->getField($field);
            }
            $this->createField($field, $arguments);
            return $this;
        }

        // Otherwise, we're asking to modify an attribute or field property.
        if (array_key_exists($name, $this->attributes)) {
            if (count($arguments) == 0) {
                return $this->attributes[$name];
            }
            $this->attributes[$name] = $arguments[0];
            return $this;
        }

        // We also might be asking to adjust a property on a field object.
        if (in_array($name, $this->fieldProperties)) {
            if (count($arguments) == 0) {
                return $this->fields->filter(function($field) use($name) {
                    return $field->$name; // Only return the fields that are true for that property.
                });
            }
            return $this->updateFieldProperty($name,$arguments[0]);
        }

        return null;
    }


    /**
     * Activate a boolean property for the given fields.
     * @param $property string fillable|guarded|in_table, etc
     * @param $arguments array|string - * if all fields
     * @return $this
     */
    protected function updateFieldProperty($property, $arguments)
    {
        $fields = $arguments == "*" ? $this->fields : $this->fields->filter(function(FieldBlueprint $field) use ($arguments) {
            return in_array($field->getName(), $arguments);
        });

        $fields->each(function(FieldBlueprint $field) use ($property) {
            $field->setProperty($property,true);
        });

        return $this;
    }


    /**
     * Helper for setting timestamps.
     * @return $this
     */
    public function useTimestamps()
    {
        $this->attributes['timestamps'] = true;

        $this->_created_at ("Created Date", FieldBlueprint::TIMESTAMP, null);
        $this->_updated_at ("Updated Date", FieldBlueprint::TIMESTAMP, null);

        $this->dates(['created_at','updated_at']);
        return $this;
    }

    /**
     * Helper for setting softdeletes.
     * @return $this
     */
    public function useSoftDeletes()
    {
        $this->attributes['softDeletes'] = true;
        $this->_deleted_at ("Deleted Date", FieldBlueprint::TIMESTAMP, null);

        $this->dates(['deleted_at']);
        return $this;
    }

    /**
     * Return the labels collection or set new labels.
     * @param $array array key=>value
     * @return Collection|$this
     */
    public function labels($array=null)
    {
        if (is_null($array)) {
            return $this->labels;
        }
        $this->labels = $this->labels->merge($array);
        return $this;
    }

    /**
     * Register a component module.
     * @param $componentClass string
     * @param $args array
     * @return $this
     */
    public function module ($componentClass,$args=[])
    {
        $this->modules[] = [$componentClass,$args];
        return $this;
    }

    /**
     * Getter for modules.
     * @return array
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Set the table name or get the table name.
     * @param $name string
     * @return $this|string
     */
    public function table($name=null)
    {
        if (! $name) {
            return $this->table;
        }
        $this->table = $name;

        if ($this->labels->isEmpty()) {
            $this->guessLabels($name);
        }
        return $this;
    }

    /**
     * Return the protected class name.
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Take a guess at what the basic labels will be, based on the given name.
     * @return $this
     */
    protected function guessLabels($name)
    {
        return $this->labels([
            'singular'   => str_replace("_"," ",Str::singular($name)),
            'plural'     => str_replace("_"," ",Str::plural($name)),
            'navigation' => Str::ucfirst($name),
            'slug'       => Str::slug(Str::plural($name))
        ]);
    }

    /**
     * Create a new field object.
     * @param $name string
     * @param $arguments array
     * @return FieldBlueprint
     */
    protected function createField($name,$arguments)
    {
        list ($label,$fieldType,$inputType) = $arguments;

        $type = is_array($fieldType) ? array_shift($fieldType) : $fieldType;
        $args = is_array($fieldType) ? $fieldType : null;

        $field = FieldBlueprint::create($name,$type,$args,$this)->withInput($label,$inputType,$this->fields->count());

        if ($type === FieldBlueprint::TITLE) {
            $this->title = $field;
        }
        return $this->fields[$name] = $field;
    }

    /**
     * Get a field by name.
     * @param $name string
     * @return FieldBlueprint|null
     */
    public function getField($name)
    {
        return $this->fields[$name];
    }


    /**
     * Return the field collection.
     * @return Collection
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Mass-assign options for given fields.
     * @param $array array column=>options
     * @return $this
     */
    public function setOptions($array)
    {
        foreach($array as $field=>$options)
        {
            if (! $this->fields->has($field)) {
                continue;
            }

            $this->getField($field)->options = $options;
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