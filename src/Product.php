<?php

namespace Birdmin;

use Birdmin\Contracts\RelatedMedia;
use Birdmin\Contracts\Sluggable;
use Birdmin\Core\Model;
use Birdmin\Collections\MediaCollection;

class Product extends Model implements Sluggable, RelatedMedia
{
    public static $repository;

    protected $appends = ['bundle','category'];

    /**
     * Return a URL for this model on the frontend.
     * @param $relative bool
     * @return string
     */
    public function url($relative=false)
    {
        $path = $this->composeUrlString($this->blueprint->url);

        return $relative ? "/$path" : url($path);
    }

    /**
     * Collection of Product Variations.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
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
     * Returns the category.
     * @return mixed
     */
    public function getCategoryAttribute()
    {
        return $this->category()->first();
    }

    /**
     * Return the product bundle object.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function bundle()
    {
        return $this->hasOne(ProductBundle::class, 'id','bundle_id');
    }

    /**
     * Returns the bundle.
     * @return mixed
     */
    public function getBundleAttribute()
    {
        return $this->bundle()->first();
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
