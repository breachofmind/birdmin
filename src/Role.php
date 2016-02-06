<?php

namespace Birdmin;

use Birdmin\Core\Model;
use Birdmin\Collections\PermissionCollection;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = ['name','description'];

    protected $dates = ['created_at','updated_at'];

    protected $searchable = ['name', 'description'];
    /**
     * Return users that have this role.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Return all permissions assigned to this role.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Grant a permission to this role.
     * @param Permission $permission
     * @return null|PermissionCollection
     */
    public function grant(Permission $permission)
    {
        // Don't assign the permission if we already have it.
        if ($this->hasPermission($permission)) {
            return null;
        }
        return $this->permissions()->attach($permission);
    }

    /**
     * Shortcut for granting an ability to this role.
     * @param $ability string
     * @param $object string|Model
     * @return PermissionCollection|null
     */
    public function grantAbility($ability,$object)
    {
        $permission = Permission::lookup($ability,$object);
        if (!$permission) {
            return null;
        }
        return $this->grant($permission);
    }

    /**
     * Deny a permission for this role.
     * @param Permission $permission
     * @return null|PermissionCollection
     */
    public function deny(Permission $permission)
    {
        if (!$this->hasPermission($permission)) {
            return null;
        }
        return $this->permissions()->detach($permission);
    }

    /**
     * Shortcut for denying an ability to this role.
     * @param $ability string
     * @param $object string|Model
     * @return PermissionCollection|null
     */
    public function denyAbility($ability,$object)
    {
        $permission = Permission::lookup($ability,$object);
        if (!$permission) {
            return null;
        }
        return $this->deny($permission);
    }

    /**
     * Check if this role has a permission.
     * @param Permission $permission
     * @return bool
     */
    public function hasPermission(Permission $permission)
    {
        return $this->permissions()->get()->contains($permission);
    }

    /**
     * Return a role by name.
     * @param $name string
     * @return mixed
     */
    public static function getByName($name)
    {
        return Role::where('name',$name)->first();
    }

    /**
     * Give this role to a user.
     * @param User $user
     * @return null|Collection
     */
    public function assign (User $user)
    {
        if ($user->hasRole($this)) {
            return null;
        }
        return $user->roles()->attach($this);
    }
}
