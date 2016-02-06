<?php

namespace Birdmin\Collections;

use Birdmin\Media;
use Birdmin\Core\Model;
use Illuminate\Database\Eloquent\Collection;

class MediaCollection extends Collection
{

    /**
     * Constructor.
     * @param array $items
     */
    public function __construct($items = []) {
        parent::__construct($items);
    }

    /**
     * Return a new collection of media, given the native type.
     * @param $type string (image,document,vector...)
     * @return MediaCollection
     */
    public function byType($type) {
        $collection = new MediaCollection();
        foreach ($this->items as $media) {
            if ($media->nativeType() == strtolower($type)) {
                $collection->add($media);
            }
        }
        return $collection;
    }
}