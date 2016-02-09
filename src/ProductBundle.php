<?php

namespace Birdmin;

use Birdmin\Contracts\Sluggable;
use Birdmin\Core\Model;
use Birdmin\Contracts\RelatedMedia;

class ProductBundle extends Model
    implements Sluggable, RelatedMedia
{
    protected $table = "product_bundles";

    protected $fillable = [
        'name',
        'brand',
        'excerpt',
        'description',
        'slug',
        'status',
        'website'
    ];

    protected $searchable = ['name','brand','status'];

    /**
     * Return the slug URL.
     * @param bool $relative
     * @return string
     */
    public function url($relative=false)
    {
        // TODO dynamic
        return "/bundle/".$this->slug;
    }

    /**
     * Return related media.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function media()
    {
        return $this->related(Media::class);
    }
}
