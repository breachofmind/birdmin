<?php
namespace Birdmin;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Birdmin\Core\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class Relationship extends BaseModel {

    protected $table = "relationships";

    protected $fillable = [
        'priority',
        'parent_id',
        'parent_object',
        'child_id',
        'child_object'
    ];

    /**
     * Return the child relationships of a given class/id and child class.
     * @param string $parent_object
     * @param null|int $parent_id
     * @param null|string $child_object
     * @return mixed
     */
    public static function children($parent_object, $parent_id=null, $child_object=null)
    {
        $relationships = static::where('parent_object',$parent_object);
        if ($parent_id) $relationships->where('parent_id',$parent_id);
        if ($child_object) $relationships->where('child_object', $child_object);
        return $relationships->orderBy('priority', 'asc')->get();
    }

    /**
     * Return the parent relationships of a given child class/id andaa parent class.
     * @param string $child_object
     * @param null|int $child_id
     * @param null|string $parent_object
     * @return mixed|Collection
     */
    public static function parents($child_object, $child_id=null, $parent_object=null)
    {
        $relationships = static::where('child_object',$child_object);
        if ($child_id) $relationships->where('child_id',$child_id);
        if ($parent_object) $relationships->where('parent_object', $parent_object);
        return $relationships->orderBy('priority','asc')->get();
    }

    /**
     * Return a collection of the child object, given the parent object.
     * @param Model $model
     * @param string|Model $child_object
     * @return Collection
     */
    public static function collection (Model $model, $child_object)
    {
        if ($child_object instanceof Model) {
            $child_object = $child_object->getClass();
        }
        $relationships = Relationship::children(get_class($model), $model->id, $child_object);
        if ($relationships->isEmpty()) {
            $child_instance = new $child_object;
            return $child_instance->newCollection();
        }
        $ids = $relationships->pluck('child_id')->toArray();
        return $child_object::whereIn('id', $ids)
            ->orderBy(DB::raw('FIELD(`id`, '.join(",",$ids).')'))
            ->get();
    }

    /**
     * Check if a relationship exists between two objects.
     * @param Model $parent
     * @param Model $child
     * @return null|Collection
     */
    public static function exists(Model $parent, Model $child)
    {
        $items = static::where('parent_id',$parent->id)
            ->where('parent_object', $parent->getClass())
            ->where('child_id', $child->id)
            ->where('child_object', $child->getClass())
            ->get();
        return $items->count() > 0;
    }

    /**
     * Relate an object to a child object.
     * @param Model $parent
     * @param Model $child
     * @return static
     */
    public static function relate(Model $parent, Model $child)
    {
        return Relationship::create([
            'parent_id' => $parent->id,
            'child_id' => $child->id,
            'parent_object' => $parent->getClass(),
            'child_object' => $child->getClass(),
            'priority' => Relationship::lastPriority($parent,$child)
        ]);
    }


    /**
     * Get the last priority number for a relationship.
     * @param Model $parent
     * @param $child_object
     * @return int
     */
    public static function lastPriority(Model $parent, $child_object)
    {
        $rel = DB::table('relationships')
            ->select(DB::raw('MAX(`priority`) as `last_priority`'))
            ->where('parent_id', $parent->id)
            ->where('parent_object', get_class($parent))
            ->where('child_object', $child_object)
            ->get();
        return $rel ? (int)$rel[0]->last_priority+1 : (int)0;
    }
}