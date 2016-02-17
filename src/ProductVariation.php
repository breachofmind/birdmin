<?php

namespace Birdmin;

use Birdmin\Contracts\RelatedMedia;
use Birdmin\Core\Model;


class ProductVariation extends Model implements RelatedMedia
{
    protected $joins = [
        'product_id' => Product::class
    ];

    /**
     * Return the product for this variation.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function getProductAttribute()
    {
        return $this->product()->first();
    }

    public function media()
    {
        return $this->related(Media::class);
    }
}
