<?php
namespace Birdmin\Support;


class IndexTableBlueprint {

    protected $bulk = true;

    /**
     * Columns to add.
     * @var array
     */
    protected $columns = [];

    /**
     * Formatters to apply.
     * @var array
     */
    protected $formatters = [];

    /**
     * The parent model blueprint.
     * @var ModelBlueprint
     */
    protected $blueprint;

    /**
     * IndexTableBlueprint constructor.
     * @param ModelBlueprint $modelBlueprint
     */
    public function __construct(ModelBlueprint $modelBlueprint)
    {
        $this->blueprint = $modelBlueprint;
    }

    /**
     * Add columns.
     * @param array $array
     * @return $this
     */
    public function columns($array=[])
    {
        foreach ($array as $field=>$args)
        {
            list ($label,$priority,$formatter) = $args;

            $this->columns[$field] = $args;
        }
        return $this;
    }

    /**
     * Add column formatters.
     * @param array $array
     * @return $this
     */
    public function formatters($array=[])
    {
        foreach ($array as $field=>$formatter)
        {
            $this->formatters[$field] = $formatter;
        }
        return $this;
    }

    /**
     * Use bulk editing?
     * @param bool $bool
     * @return $this
     */
    public function bulk($bool=true)
    {
        $this->bulk = $bool;
        return $this;
    }

}