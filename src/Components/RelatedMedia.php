<?php
namespace Birdmin\Components;

use Birdmin\Collections\MediaCollection;
use Birdmin\Contracts\Hierarchical;
use Birdmin\Core\Model;
use Birdmin\Core\Component;
use Birdmin\Contracts\ModuleComponent;
use Illuminate\Http\Request;

class RelatedMedia extends Component implements ModuleComponent
{
    protected $name = "Related Media";

    protected $view = "cms::components.related-media";

    protected $icon = "picture";

    /**
     * The parent model.
     * @var Model
     */
    protected $model;

    /**
     * The collection of media related to the model.
     * @var MediaCollection
     */
    protected $media;

    /**
     * Dropzone component.
     * @var Dropzone
     */
    protected $dropzone;

    /**
     * RelatedMedia constructor.
     * @param Model $model
     */
    public function __construct(Model $model, $args=null)
    {
        $this->parent($model);
        $this->dropzone = Dropzone::create()
            ->handler('relate', cms_url('media/upload'))
            ->relate($model);
    }

    /**
     * Return an array of properties, which will also be in json_encode.
     * @return array
     */
    public function toArray()
    {
        return $this->compact('model','media','dropzone','icon');
    }

    public function prepare()
    {
        $this->with($this->toArray());
        $this->data['dropzoneId'] = $this->dropzone->id;
    }

    /**
     * Attach the parent model.
     * @param Model $model
     * @return $this
     */
    public function parent(Model $model)
    {
        $this->model = $model;
        $this->media = $model->media();
        return $this;
    }
}