<?php

namespace Birdmin;

use Birdmin\Contracts\Sluggable;
use Birdmin\Core\Model;
use Birdmin\Contracts\Hierarchical;
use Birdmin\Support\HTMLProcesser;
use Birdmin\Support\Traits\Tree;
use Illuminate\Http\Request;
use Sunra\PhpSimple\HtmlDomParser;


class Page extends Model
    implements Hierarchical, Sluggable
{
    use Tree;

    protected $appends = ['children'];

    /**
     * The processed HTML content.
     * @var HTMLProcesser
     */
    private $processed;

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
    public function getContent($id=null)
    {
        if (! $this->processed) {
            try {
                $this->processed = HTMLProcesser::parse($this->content)->uses($this);

            } catch(\Exception $e) {
                return $this->content;
            }
        }

        return is_string($id) ? $this->processed->getId($id) : $this->processed->render();
    }
}
