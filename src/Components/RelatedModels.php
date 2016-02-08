<?php
namespace Birdmin\Components;

use Birdmin\Collections\MediaCollection;
use Birdmin\Contracts\Hierarchical;
use Birdmin\Core\Model;
use Birdmin\Core\Component;
use Birdmin\Contracts\ModuleComponent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Birdmin\Components\ButtonGroup;

class RelatedModels extends Component implements ModuleComponent
{
    protected $name = "Related Models";

    protected $view = "cms::components.related-models";

    /**
     * The parent model.
     * @var Model
     */
    protected $model;

    /**
     * The child related class.
     * @var string
     */
    protected $class;

    /**
     * The collection of related objects.
     * @var Collection
     */
    protected $related;

    /**
     * Action buttons.
     * @var ButtonGroup
     */
    protected $actions;

    /**
     * RelatedMedia constructor.
     * @param Model $model
     */
    public function __construct(Model $model, $args=null)
    {
        $this->actions = ButtonGroup::create();
        $this->parent($model);
        if ($args) $this->child($args[0]);
    }

    /**
     * Attach the parent model.
     * @param Model $model
     * @return $this
     */
    public function parent(Model $model)
    {
        $this->model = $model;
        return $this;
    }

    public function child($class)
    {
        if (class_exists($class)) {
            $this->class = $class;
            $method = $class::plural();
            $this->related = method_exists($this->model, $method) ? $this->model->$method()->get() : $this->model->related($class);
            $this->actions->add(Button::create()->parent($class)->link('create')->classes('button success'));
        }
        return $this;
    }
    /**
     * Return an array of properties, which will also be in json_encode.
     * @return array
     */
    public function toArray()
    {
        return $this->compact('class','model','actions','related');
    }



}