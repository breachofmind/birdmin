<?php

namespace Birdmin;

use Birdmin\Core\Model;
use Birdmin\Contracts\Hierarchical;
use Birdmin\Contracts\Sluggable;
use Birdmin\Support\Traits\Tree;

class Category extends Model implements Hierarchical, Sluggable
{
    use Tree;

    protected $appends = ['children'];

    public static $repository = [];

    /**
     * Return the URL string to this object.
     * @return string
     */
    public function url($relative=false)
    {
        if ($pattern = $this->getBlueprint('url'))
        {
            $path = stringf($pattern, $this->toArray());
            return $relative ? "/$path" : url($path);
        }

        return $this->assembleSlugFrom($this->parents()->reverse(), $relative);
    }
}
