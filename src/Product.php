<?php

namespace Birdmin;

use Birdmin\Contracts\Sluggable;
use Birdmin\Core\Model;
use Birdmin\Collections\MediaCollection;

class Product extends Model
    implements Sluggable
{
    protected $table = "products";

    protected $fillable = [
        'name',
        'category_id',
        'brand',
        'excerpt',
        'description',
        'attributes',
        'sku',
        'type',
        'status',
        'slug'
    ];

    protected $searchable = ['name', 'slug', 'status', 'type','sku','brand'];

    public static $repository;

    public $timestamps = true;

    /**
     * Return a URL for this model on the frontend. TODO - dynamic
     * @param $relative bool
     * @return string
     */
    public function url($relative=false)
    {
        $url = "products/".$this->slug;
        return $relative ? "/".$url : url($url);
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    /**
     * Return the main category of this product (if applicable)
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function category()
    {
        return $this->hasOne(Category::class, 'id','category_id');
    }

    /**
     * Return a collection of media items, ordered by priority.
     * @return MediaCollection
     */
    public function media()
    {
        return $this->related(Media::class);
    }
}
