<?php

namespace Birdmin;

use Birdmin\Contracts\Sluggable;
use Birdmin\Core\Model;
use Birdmin\Contracts\Hierarchical;
use Birdmin\Support\Traits\Tree;
use Illuminate\Http\Request;


class Page extends Model
    implements Hierarchical, Sluggable
{
    use Tree;

    protected $appends = ['children'];

    public static $repository;

    /**
     * Return a URL for this model on the frontend.
     * @param $relative bool
     * @return string
     */
    public function url($relative=false)
    {
        return $this->assembleSlugFrom($this->parents()->reverse(), $relative);
    }

    /**
     * Return the content for the page, or blocks of content.
     * @return array|string
     */
    public function getContent($block=null)
    {
        $content = $this->getAttribute('content');
        return $content;
    }
}
