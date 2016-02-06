<?php
namespace Birdmin\Contracts;

use Illuminate\Http\Request;

interface Sluggable {

    /**
     * Should return a URL string, composed of the slug.
     * @param $relative bool
     * @return string
     */
    public function url($relative=false);
}