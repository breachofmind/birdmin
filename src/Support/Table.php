<?php
namespace Birdmin\Support;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Collection;
use Birdmin\Input;
use Birdmin\Core\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

class Table implements Jsonable, Renderable {
    /**
     * Universal table class for building list views.
     * Tables are associated with Collections.
     * @package Birdmin
     */

    protected $class;

    /**
     * Collection of items.
     * @var Collection
     */
    protected $items;

    /**
     * Column names and labels.
     * @var array
     */
    protected $headers = [
        //'column_name' => 'Label'
    ];

    /**
     * Order that each field will be printed, left to right.
     * @var array
     */
    protected $order = [
        //'column_name' => 0
    ];

    /**
     * Formatter callbacks for table columns.
     * @var array
     */
    protected $formatters = [
        //'column_name' => function($model,field) {}
    ];

    protected $paginator;

    public $bulk = false;

    /**
     * Constructor.
     * @param Collection|LengthAwarePaginator $collection
     * @param string $class - optional
     */
    public function __construct($collection, $class=null)
    {
        if ($collection instanceof LengthAwarePaginator) {
            $this->paginator = $collection;
            $this->items = $collection->getCollection();
        } else {
            $this->items = $collection;
        }
        if ($class) {
            $this->setClass($class);
        }
    }

    /**
     * Named constructor, for chaining.
     * @param $colleciton
     * @param null|string $class
     * @return static
     */
    public static function create($collection,$class=null)
    {
        return new static($collection,$class);
    }

    /**
     * Add a new column to the table.
     * A column should have an associated formatter or model property.
     * @param string $field
     * @param null|string $label
     * @param int $priority
     * @param Closure|string $formatter function, optional
     * @return $this
     */
    public function setHeader ($field, $label=null, $priority=0, $formatter=null)
    {
        if (is_null($label)) {
            $label = $field;
        }
        $this->headers[$field] = $label;
        $this->setPriority($field,$priority);
        if ($formatter) {
            $this->setFormatter($field, $formatter);
        }
        return $this;
    }

    /**
     * Return the total number of columns.
     * @return int
     */
    public function totalColumns ()
    {
        return count($this->order);
    }

    /**
     * Return the total number of rows in the table.
     * @return int
     */
    public function totalItems ()
    {
        if ($this->paginator) {
            return $this->paginator->total();
        }
        return $this->items->count();
    }

    /**
     * Adjust the priority of a column.
     * @param string $field
     * @param int $priority
     * @return $this
     */
    public function setPriority ($field, $priority=0)
    {
        $this->order[$field] = (int)$priority;
        asort($this->order);
        return $this;
    }

    /**
     * Associate a model class this with table.
     * Loads the appropriate table headers and priorities.
     * Object title fields will display an edit link (pending user permissions)
     * @param string $class
     * @return $this|bool
     */
    public function setClass ($class)
    {
        if (!class_exists($class)) {
            return false;
        }
        $this->class = $class;
        $inputs = Input::byObject($class);
        $inputs->each(function($input) use ($class) {
            if (!$input->in_table) {
                return;
            }
            // Create a link to edit the object.
            if ($input->isTitleField()) {
                $this->setFormatter($input->name, '\Birdmin\Formatters\edit_model_link');
            }
            $this->setHeader($input->name, $input->label, $input->priority);
        });
//        if ($config = $class::getConfig()) {
//            $this->config(isset($config['table']) ? $config['table'] : array());
//        }
        return $this;
    }

    /**
     * Add a table configuration.
     * Consists of formatters=>[field=>callable]
     * and columns=>[field=>[label,priority,callable]]
     * @param array $config
     * @return $this
     */
    public function config(array $config)
    {
        if (empty($config)) {
            return $this;
        }
        $formatters = array_get($config,'formatters');
        $columns = array_get($config,'columns');
        $this->bulk = (boolean)array_get($config,'bulk');
        foreach ((array)$columns as $field=>$settings) {
            list($label,$priority,$formatter) = $settings;
            $this->setHeader($field,$label,$priority,$formatter);
        }
        foreach ((array)$formatters as $field=>$formatter) {
            $this->setFormatter($field,$formatter);
        }
        return $this;
    }

    /**
     * Associate a cell formatter with a field.
     * Formatters will morph the cell values to your liking.
     * @param string $field  field name
     * @param Closure|string $callable function name
     * @return $this
     */
    public function setFormatter ($field,$callable)
    {
        if (is_string($callable)) {
            $functionName = $callable;
            $callable = function($model,$fieldname) use ($functionName) {
                return call_user_func($functionName, $model,$fieldname);
            };
        }
        $this->formatters[$field] = $callable;
        return $this;
    }

    /**
     * Return the objects in this table.
     * @return Collection
     */
    public function getItems ()
    {
        return $this->items;
    }

    /**
     * Return the header label for the given field.
     * @param string $field
     * @return mixed
     */
    public function getHeader ($field)
    {
        return $this->headers[$field];
    }

    /**
     * Return the column order in $field=>$priority.
     * @return array
     */
    public function getOrder ()
    {
        return $this->order;
    }

    /**
     * Return the value of a cell in the table.
     * @param Model $model
     * @param string $field field name
     * @return mixed
     */
    public function getCell (Model $model,$field)
    {
        if (array_key_exists($field, $this->formatters)) {
            $closure = $this->formatters[$field];
            return $closure($model,$field);
        }
        return $model->getAttribute($field);
    }

    /**
     * Check if the given column is sortable in the database.
     * Only columns that are in the database can be sorted on.
     * @param $column
     * @return bool|string asc|desc
     */
    protected function isSortable ($column)
    {
        $class = $this->class;
        if (!in_array($column, with(new $class)->getFillable())) {
            return false;
        }
        if (Request::input('orderby') == $column) {
            return Request::has('dir') ? Request::input('dir') : "asc";
        }
        return true;
    }

    protected function prepare ()
    {
        if ($this->bulk) {
            $this->setHeader('_bulk',"",-100,'Birdmin\Formatters\bulk');
        }
        asort($this->order);
        return true;
    }

    /**
     * Render the table.
     * @return string
     */
    public function render()
    {
        $this->prepare();
        return view('cms::table.body', ['table' =>$this])->render();
    }

    /**
     * Echo this object as a string.
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Return the table as a json-encoded string.
     * @return string|array
     */
    public function toJson($encode=false)
    {
        $this->prepare();
        $table = [
            'headers'       => [],
            'rows'          => [],
            'total'         => $this->totalItems(),
            'lastPage'      => $this->paginator ? $this->paginator->lastPage() : null,
            'currentPage'   => $this->paginator ? $this->paginator->currentPage() : null,
            'orderby'       => Request::input('orderby'),
            'dir'           => Request::input('dir'),
        ];

        foreach ($this->order as $column=>$priority) {
            $table['headers'][$column] = [
                'label' => $this->getHeader($column),
                'orderby' => $this->isSortable($column)
            ];
        }
        foreach ($this->items as $i=>$model) {
            $table['rows'][$i] = [];
            foreach ($this->order as $column=>$priority) {
                $table['rows'][$i][$column] = utf8_encode($this->getCell($model,$column));
            }
        }
        return $encode ? json_encode($table) : $table;
    }
}