<?php
namespace Birdmin;

use Birdmin\Collections\MetaCollection;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Birdmin\Core\Model;

class Meta extends BaseModel {

    protected $table = "meta";

    protected $fillable = [
        'object',
        'key',
        'value',
    ];

    public $timestamps = false;

    /**
     * Search the metadata table data associated with the given model.
     * Assigns a new attribute inside of the model - 'meta'.
     * @return MetaCollection
     */
    public static function retrieve (Model $model)
    {
        if ($model->meta) {
            return $model->meta;
        }
        // Object can either be the UID or the model class.
        // Model class tends to be global for all of that model.
        // However, UID keys will override class keys.
        return static::where('object', $model->getAttribute('uid'))
            ->orWhere('object', get_class($model))
            ->get();
    }

    /**
     * Return a special collection of metadata.
     * @param array $models
     * @returns MetaCollection
     */
    public function newCollection(array $models = [])
    {
        return new MetaCollection($models);
    }
}