<?php
namespace Birdmin\Components;

use Birdmin\Collections\MediaCollection;
use Birdmin\Contracts\Hierarchical;
use Birdmin\Core\Model;
use Birdmin\Core\Component;
use Birdmin\Contracts\ModuleComponent;
use Illuminate\Http\Request;

class Dropzone extends Component
{
    protected $name = "Media Dropzone";

    protected $view = "cms::components.dropzone";

    protected $action = "/media/upload";

    protected $handler = "default";

    public $id = "MediaDropzone";

    public $relateTo = [];


    public function handler ($name, $url=null)
    {
        $this->handler = $name;
        if ($url) $this->action = $url;
        return $this;
    }

    /**
     * Setup the POST action to relate the given model to the uploaded media.
     * Note - the model will always be the PARENT of the media.
     * @param Model $model
     * @return $this
     */
    public function relate(Model $model)
    {
        $this->relateTo[] = $model;
        return $this;
    }

    /**
     * Return an array of properties, which will also be in json_encode.
     * @return array
     */
    public function toArray()
    {
        return $this->compact('action','handler','id', 'relateTo');
    }
}