<?php
namespace Birdmin\Support\Traits;

use Illuminate\Database\Eloquent\Collection;

trait Tree {

    /**
     * Return a collection of children to this object.
     * @return array|null
     */
    public function getChildrenAttribute ()
    {
        return $this->children()->get();
    }

    /**
     * Return this object's parent.
     */
    public function parent()
    {
        $this->hasOne(static::class, 'id','parent_id');
    }

    /**
     * Return the children of this object.
     * @return mixed
     */
    public function children()
    {
        return static::where('parent_id',$this->id);
    }

    /**
     * Return all the parent objects.
     * @return Collection
     */
    public function parents()
    {
        return $this->ascendTree();
    }

    /**
     * Return just the parent objects of this model.
     * @return mixed
     */
    public static function roots()
    {
        return static::where('parent_id', 0);
    }

    /**
     * Return a map of parent objects and their children.
     * @return array
     */
    public static function map()
    {
        if (empty(static::$repository)) {
            static::$repository = static::all();
        }
        $map = [];
        foreach (static::$repository as $model) {
            $map[$model->id] = $model;
        }
        return $map;
    }

    /**
     * For models apart of a tree structure, return all parent objects.
     * @param string $field - default is parent_id
     * @return Collection
     */
    protected function ascendTree($field="parent_id")
    {
        $parents = $this->newCollection([]);
        $map = static::map();
        $getParent = function($child) use($map,$parents,$field,&$getParent) {
            if (!$child->$field) {
                return;
            }
            $parents->add($map[$child->$field]);
            $getParent ($map[$child->$field]);
        };
        $getParent($this);

        return $parents;
    }
}