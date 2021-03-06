<?php

namespace Birdmin\Core;

use Birdmin\Contracts\Sluggable;
use Birdmin\Events\ModelConstruct;
use Birdmin\Media;
use Birdmin\Permission;
use Birdmin\Support\ModelBlueprint;
use Birdmin\User;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\Collection;
use Birdmin\Meta;
use Birdmin\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Birdmin\Relationship;
use Birdmin\Collections\InputCollection;
use Birdmin\Collections\MetaCollection;

class Model extends BaseModel
{
    public static $map = [];
    public static $config = [];
    public static $components = [];

    /**
     * Collection of inputs.
     * @var InputCollection
     */
    public $inputs;

    /**
     * Collection of metadata.
     * @var MetaCollection
     */
    protected $meta;

    /**
     * Column name and class to perform a join.
     * Useful for doing complex searches.
     * @var array [column=>class]
     */
    protected $joins = [];

    /**
     * The blueprint for this model.
     * @var ModelBlueprint
     */
    protected $blueprint;

    /**
     * Columns that are mass-searchable.
     * @var array
     */
    protected $searchable = [];

    protected $_components = [];

    /**
     * Called on system boot.
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Add the uid field.
        static::creating(function($model)
        {
            $model->uid = "b".sha1(uniqid().time());
        });
    }


    /**
     * Constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->configureModel ( $this::blueprint() );

        parent::__construct($attributes);

        // Create component objects if registered.
        if (array_key_exists(static::class, Model::$components)) {
            foreach (Model::$components[static::class] as $componentClass) {
                $this->_components[] = $componentClass::create($this);
            }
        }
    }


    /**
     * Modify protected attributes at runtime.
     * This is a way to override a models defaults on a client by client basis.
     * @param ModelBlueprint $blueprint|null
     * @return void
     */
    protected function configureModel (ModelBlueprint $blueprint=null)
    {
        if (empty($blueprint)) {
            return null;
        }
        $this->blueprint = $blueprint;
        $this->table = $blueprint->table();

        $this->appends = array_merge($this->appends, ['objectUrl','objectEditUrl','objectTitle']);

        if (!($this instanceof Media)) {
            $this->appends[] = "objectImage";
        }

        foreach( ['fillable',' guarded', 'hidden', 'dates', 'searchable'] as $property )
        {
            if ($blueprint->$property())
            $this->$property = array_merge($this->$property, $blueprint->$property()->keys()->toArray());
        }

        $this->timestamps = $blueprint->timestamps;
    }

    /**
     * Return the currently set model blueprint object or property..
     * @return ModelBlueprint
     */
    public function getBlueprint($property=null)
    {
        return $property ? $this->blueprint->$property : $this->blueprint;
    }

    /**
     * Return the blueprint object from the collection.
     * @return ModelBlueprint|null
     */
    public static function blueprint()
    {
        return ModelBlueprint::get(static::class);
    }

    /**
     * Modified getter.
     * @param string $key
     * @return \Birdmin\Collections\MetaCollection|mixed|null|string
     */
    public function __get($key)
    {
        if ($key=='meta') return $this->$key;

        // Attempt to get the value first, which may be an attribute or relation.
        $value = $this->getAttribute($key);
        if (!is_null($value)) {
            return $value;
        }
        // Otherwise, attempt to retrieve from meta.
        return $this->meta($key);
    }

    /**
     * Utility for getting the class name from a variable.
     * @return string
     */
    public static function getClass()
    {
        return get_called_class();
    }

    /**
     * Find inputs for this object and set the values.
     * @return Collection
     */
    public function inputs()
    {
        return $this->inputs = Input::retrieve($this);
    }

    /**
     * Return the metadata collection.
     * This object facilitates extending objects without modifying the database schema.
     * @param string $key - optional for returning a quick value.
     * @return \Birdmin\Collections\MetaCollection|string|null
     */
    public function meta($key=null)
    {
        $this->meta = Meta::retrieve($this);
        return $key ? $this->meta->$key : $this->meta;
    }

    /**
     * Save/update meta data to this object.
     * @param $key string
     * @param $value string
     * @return static
     */
    public function saveMeta($key,$value)
    {
        if ($meta = $this->meta()->getMeta($key)) {
            return $meta->update(['value'=>$value]);
        }
        return Meta::create([
            'object' => $this->uid,
            'key'    => $key,
            'value'  => $value
        ]);
    }


    /**
     * Save a global key value pair for a Model class.
     * @param $key string
     * @param $value string
     * @return static
     */
    public static function saveGlobalMeta($key,$value)
    {
        $class = static::getClass();
        $static = new $class;
        if ($meta = $static->meta()->getGlobal($key)) {
            return $meta->update(['value'=>$value]);
        }
        return Meta::create([
            'object' => $class,
            'key'    => $key,
            'value'  => $value
        ]);
    }


    /**
     * Return the column names that are searchable in the database.
     * If not defined, only looks through fillable fields.
     * @return array|mixed
     */
    public function getSearchable()
    {
        if (empty($this->searchable)) {
            return $this->fillable;
        }
        return $this->searchable;
    }

    /**
     * Return the object name.
     * i.e. Birdmin\User\1
     * @return string
     */
    public function getObjectNameAttribute()
    {
        return $this->getClass()."\\".$this->id;
    }

    /**
     * Return the title field of this model.
     * @return mixed
     */
    public function getTitle()
    {
        return $this->getAttribute( static::getTitleField() );
    }

    /**
     * Accessor alias for getTitle().
     * @return string|null
     */
    public function getObjectTitleAttribute()
    {
        return $this->getTitle();
    }

    /**
     * Return the object's REST url.
     * @return string
     */
    public function getObjectUrlAttribute()
    {
        return url('api/v1/'.static::getLabel('slug').'/'.$this->id);
    }

    /**
     * Return the first object image.
     * @return Media|null
     */
    public function getObjectImageAttribute()
    {
        return $this->getImage();
    }

    /**
     * Return the edit URL string.
     * @return string
     */
    public function getObjectEditUrlAttribute()
    {
        return $this->editUrl();
    }

    /**
     * Return the defined title field.
     * @return mixed
     */
    public static function getTitleField()
    {
        return static::blueprint()->getTitleField();
    }

    /**
     * Return a label value from this model.
     * @param string $name
     * @return string
     */
    public static function getLabel($name)
    {

        return static::blueprint()->label($name);
    }

    /**
     * Set a label value for this model.
     * @param string $name
     * @param string $value
     * @return void
     */
    public static function setLabel($name,$value)
    {
        static::blueprint()->labels([$name=>$value]);
    }

    /**
     * Return the class icon name.
     * @return mixed
     */
    public static function getIcon()
    {
        return static::blueprint()->icon;
    }

    /**
     * Shortcut to label attributes.
     * @return string
     */
    public static function singular($uc=false)
    {
        $label = static::getLabel('singular');
        return $uc ? ucwords($label) : $label;
    }
    /**
     * Shortcut to label attributes.
     * @return string
     */
    public static function plural($uc=false)
    {
        $label = static::getLabel('plural');
        return $uc ? ucwords($label) : $label;
    }

    /**
     * Relate a child object to this object.
     * Uses the 'relationships' polymorphic table.
     * Does not relate the objects if the relationship exists already.
     * @param Model $child
     * @return null|Relationship
     */
    public function relate(Model $child)
    {
        if (Relationship::exists($this,$child)) {
            return null;
        }
        return Relationship::relate($this,$child);
    }

    /**
     * Find related child objects of this model.
     * @param $child string|Model
     * @return Collection
     */
    public function related($child)
    {
        return Relationship::collection($this, $child);
    }

    /**
     * Return an image html element of this object, if any.
     * Will look for an assigned image file type.
     * @param null|string $size
     * @param null|string|array $classes
     * @param array $attrs
     * @return string|void
     */
    public function img($size=null,$classes=null,$attrs=[])
    {
        $image = $this->getImage();
        return $image ? $image->img($size,$classes,$attrs) : $this->noImage($classes);
    }

    /**
     * Get the first image object.
     * @return null|Media
     */
    public function getImage()
    {
        if (! method_exists($this,'media')) {
            return null;
        }
        $images = $this->media()->byType('image');
        return $images->first();
    }

    /**
     * Shortcut for img('sm')
     * @param null|string|array $classes
     * @return string|void
     */
    public function thumb($classes=null)
    {
        return $this->img('sm',$classes);
    }

    /**
     * Return the 'Missing Image' Image.
     * Each model can have it's own 'Missing Image' static var (defined in config file).
     * @param null|string|array $classes
     * @param array $attrs
     * @return string
     */
    public function noImage($classes=null,$attrs=[])
    {
        $missing_image_src = static::blueprint()->no_image;
        if (!$missing_image_src) {
            $missing_image_src = config('media.missing_image');
        }
        $attr = [
            'alt'=>'Image not found',
            'src' => $missing_image_src,
            'class' => $classes
        ];
        return  attributize(array_merge($attr,$attrs),'img');
    }

    /**
     * Returns a link to edit this object in the CMS.
     * @return string
     */
    public function editUrl()
    {
        return cms_url(static::getLabel('slug')."/edit/".$this->id);
    }


    /**
     * From a collection of models with slugs, create a URL string.
     * @param $collection Collection
     * @param $relative bool
     * @return string
     */
    protected function assembleSlugFrom($collection, $relative=false)
    {
        if ($collection->isEmpty()) {
            return $relative ? "/".$this->slug : url($this->slug);
        }
        $collection->add($this);
        $segments = $collection->pluck('slug')->toArray();
        $url = join("/",$segments);

        return $relative ? "/".$url : url($url);
    }

    /**
     * Composes a string in the given format.
     * @param $format string
     * @return mixed
     */
    protected function composeUrlString($format)
    {
        $matches = [];

        preg_match_all ("/\{([^}]+)\}/", $format, $matches);

        list ($str,$keys) = $matches;
        if (empty($str)) return $format;

        foreach($keys as $i=>$key)
        {
            $value = $this->getAttribute($key);
            if (! $value) {
                continue;
            }
            if ($value instanceof Sluggable) {
                $value = $value->slug;
            }
            $format = str_replace ($str[$i],$value,$format);
        }

        return $format;
    }

    /**
     * Find the first model to match the given slug.
     * @param $request Request|string
     * @param $fail boolean - fail if not found?
     * @return mixed
     */
    public static function findSlug($request, $fail=false)
    {
        $slug = $request instanceof Request ? collect($request->segments())->last() : $request;

        $object = static::where('slug',$slug)->first();

        return !$object && $fail ? abort(404) : $object;
    }

    /**
     * Based on the request, return a collection of models (paginated)
     * @param $request Request
     * @param $user User|null - optional
     * @param $paginate int|null - optional limit of results
     * @return Collection|LengthAwarePaginator
     */
    public static function request(Request $request, $user=null, $paginate=null)
    {
        // Use the max pagination if the requested pagination is over the limit.
        if (! is_null($paginate) && intval($paginate) <= config('app.pagination') && intval($paginate) > 0) {
            $paginate = intval($paginate);
        } else {
            $paginate = config('app.pagination');
        }

        $class = get_called_class();
        $static = new $class;
        $query = $class::select(DB::raw($static->table.".*"));


        // If joins are defined, create them.
        foreach ($static->joins as $column => $joinClass) {
            $joinStatic = new $joinClass;
            $query->leftJoin($joinStatic->table, $static->table.".$column", '=', $joinStatic->table.".id");
            $query->addSelect($joinStatic->getFillablePrepend($joinStatic::singular()));
        }

        // If passing a user, limit the resultset to items owned by this user.
        if ($user instanceof User && $class::isManaged()) {
            $query->where($class::ownerKey(),$user->id);
        }

        if ($request->has('s') && !empty($request->input('s')))
        {
            // Group the search requests into an AND/OR, if a user given.
            // This way, only items matching the user id will be returned in searches.
            $value = $request->input('s');
            $fields = with(new $class)->getSearchable();
            $query->where(function($query) use ($fields,$value) {
                foreach ($fields as $field) {
                    $query->orWhere($field,"LIKE","$value%");
                }
            });
        }

        if ($request->has('orderby')) {
            $query->orderBy($request->input('orderby'), $request->input('dir'));
        }

        return $query->paginate($paginate, ['*'], 'p', $request->input('p'))->appends($request->all());
    }

    /**
     * Prepends the given string to each column in the fillable array.
     * Used to perform join queries.
     * @param $string string
     * @return array
     */
    public function getFillablePrepend($string)
    {
        return array_map(function($column) use ($string) {

            return DB::raw($this->table.".`$column` AS ".$string."_".$column);

        },$this->fillable);
    }

    /**
     * Perform a validation on this model, given the input.
     * @param $attrs array
     * @return Validator
     */
    public function validate ($attrs=[])
    {
        $this->fill($attrs);
        $inputs = $this->inputs();
        $validator = Validator::make($this->toArray(), $inputs->rules());
        $validator->addCustomAttributes( $inputs->labels() );

        return $validator;
    }

    /**
     * Check if the 'manage' permission exists for this class.
     * @return boolean
     */
    public static function isManaged()
    {
        return Permission::index()->exists('manage',get_called_class());
    }

    /**
     * The default owner id.
     * Used for checking managed classes.
     * @return int
     */
    public function ownerId()
    {
        $key = static::ownerKey();
        return $this->$key;
    }

    /**
     * Return the owner or author key/column name (default is user_id)
     * @return string
     */
    public static function ownerKey()
    {
        $class = get_called_class();
        return isset($class::$ownerKey) ? $class::$ownerKey : 'user_id';
    }

    /**
     * Returns the array of component objects.
     * @return array
     */
    public function getComponents()
    {
        return $this->_components;
    }

    /**
     * Return array of module component objects.
     * @return array
     */
    public function getModuleComponents()
    {
        $out = [];
        $components = static::blueprint()->getModules();
        if (empty($components)) {
            return $out;
        }
        foreach($components as $item) {
            list ($class,$args) = $item;
            $out[] = $class::create($this,$args);
        }
        return $out;
    }

    /**
     * Register a component.
     * @param $componentClass string
     * @return mixed
     */
    public static function useComponent($componentClass)
    {
        if (! isset(Model::$components[static::class])) {
            Model::$components[static::class] = [];
        }
        return Model::$components[static::class][] = $componentClass;
    }

    /**
     * Look up a model given the signature.
     * Example: Birdmin\User\1 -> User with id #1
     * @param $string
     * @return null|Model
     */
    public static function str($string)
    {
        if (! $string) {
            return null;
        }
        $parts = explode('\\',$string);
        $id = array_pop($parts);
        $class = implode("\\",$parts);
        if (!class_exists($class)) {
            return null;
        }
        return $class::find(intval($id));
    }
}