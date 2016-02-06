<?php
namespace Birdmin\Contracts;

interface Hierarchical {

    /**
     * Return the objects that have the parent_id as this id.
     * @return mixed
     */
    function children();

    /**
     * Return the object matching the parent_id.
     * @return mixed
     */
    function parent();

    /**
     * Return a collection of all the objects parents, ascending the tree.
     * @return mixed
     */
    function parents();

    /**
     * Return a collection of root objects (parent_id=0)
     * @return mixed
     */
    static function roots();

    /**
     * Should return array of id=>parentId.
     * @return array
     */
    static function map();

}