<?php

namespace Birdmin;

use Birdmin\Collections\PermissionCollection;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = "permissions";

    protected $fillable = [
        'object',
        'ability',
        'description'
    ];

    public $timestamps = false;

    /**
     * A master index of all system permissions.
     * @var PermissionCollection
     */
    protected static $index;

    /**
     * Set and Return a collection of all permission objects.
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function index()
    {
        if (!empty(static::$index)) {
            return static::$index;
        }
        return static::$index = static::all();
    }

    /**
     * Return the master permission collection.
     * @return PermissionCollection
     */
    public static function getIndex()
    {
        return static::$index;
    }

    /**
     * Return the roles this permission is in.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Assign this permission to the given role.
     * @param Role $role
     * @return PermissionCollection|null
     */
    public function grant(Role $role)
    {
        return $role->grant($this);
    }

    /**
     * Shortcut to assign directly to a role name.
     * @param $roleName string
     * @return mixed
     * @throws \Exception
     */
    public function grantTo($roleName)
    {
        $role = Role::getByName($roleName);
        if (!$role) {
            throw new \Exception("'$roleName' not found, cannot assign permission");
        }
        return $role->grant($this);
    }
    /**
     * Deny this permission to the given role.
     * @param Role $role
     * @return PermissionCollection|null
     */
    public function deny(Role $role)
    {
        return $role->deny($this);
    }



    public static function lookup($ability, $object)
    {
        return static::index()->lookup($ability, $object);
    }

    /**
     * Return a new collection of permissions.
     * @param array $models
     * @return PermissionCollection
     */
    public function newCollection(array $models=[])
    {
        return new PermissionCollection($models);
    }
}
