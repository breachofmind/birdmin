<?php

namespace Birdmin;

use Birdmin\Contracts\Sluggable;
use Birdmin\Core\Model;
use Birdmin\Contracts\RelatedMedia;

class ProductBundle extends Model implements Sluggable, RelatedMedia
{

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
     * Return related media.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function media()
    {
        return $this->related(Media::class);
    }
}
