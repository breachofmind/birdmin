<?php

namespace Birdmin;

use Birdmin\Core\Model;
use Birdmin\Contracts\Hierarchical;
use Birdmin\Contracts\Sluggable;
use Birdmin\Support\Traits\Tree;

class Category extends Model
    implements Hierarchical, Sluggable
{
    use Tree;

    protected $table = "categories";

    protected $fillable = [
        'name',
        'description',
        'excerpt',
        'parent_id',
        'object',
        'slug'
    ];

    protected $searchable = ['name', 'slug', 'object'];
    protected $appends = ['children'];

    public static $repository = [];

    /**
     * Return the URL string to this object.
     * @return string
     */
    public function url($relative=false)
    {
        $this->assembleSlugFrom($this->parents()->reverse(), $relative);
    }
}
