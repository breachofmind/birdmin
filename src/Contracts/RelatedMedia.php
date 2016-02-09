<?php
namespace Birdmin\Contracts;

use Birdmin\Collections\MediaCollection;

interface RelatedMedia {

    /**
     * Returns a collection of related media.
     * @return MediaCollection
     */
    public function media();
}