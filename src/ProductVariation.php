<?php

namespace Birdmin;

use Birdmin\Core\Model;


class ProductVariation extends Model
{
    protected $table = "product_variations";

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'description',
        'status',
        'attributes',
        'color',
    ];

    protected $searchable = ['name','product_id', 'sku', 'status'];
    protected $joins = [
        'product_id' => Product::class
    ];

    public $timestamps = true;

    /**
     * Return the product for this variation.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
