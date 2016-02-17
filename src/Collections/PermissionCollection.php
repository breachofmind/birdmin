<?php

namespace Birdmin\Collections;

use Illuminate\Database\Eloquent\Collection;
use Birdmin\Role;
use Birdmin\Permission;
use Birdmin\Core\Model;

class PermissionCollection extends Collection
{

    protected $index = [];

    /**
     * Constructor.
     * @param array $items
     */
    public function __construct($items = [])
    {
        parent::__construct($items);
        $this->index();
    }

    /**
     * Add an item to the collection.
     * @param  $item
     * @return $this
     */
    public function add($item)
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * Merge a role's permissions with this collection.
     * Skips permissions that overlap.
     * @param Role $role
     * @return $this
     */
    public function addRole(Role $role)
    {
        foreach ($role->permissions as $permission)
        {
            if ($this->contains($permission)) {
                continue;
            }
            $this->add($permission);
        }
        $this->index();
        return $this;
    }

    /**
     * Add multiple permissions from multiple roles.
     * @param array $roles
     * @return $this
     */
    public function addRoles($roles=[])
    {
        foreach ($roles as $role) {
            $this->addRole($role);
        }
        return $this;
    }

    /**
     * Lookup a permission object in the array.
     * @param $ability string
     * @param $object Model|string
     * @return Permission|null
     */
    public function lookup ($ability, $object)
    {
        if ($object instanceof Model) {
            $object = $object->getClass();
        }
        return array_get($this->index, "$object.$ability", null);
    }

    /**
     * Check if the ability exists.
     * @param $ability string
     * @param $object string|Model
     * @return bool
     */
    public function exists ($ability, $object)
    {
        return $this->lookup($ability,$object) ? true : false;
    }

    /**
     * Generate a permission index for fast searching.
     * @return array
     */
    public function index()
    {
        $index = [];
        foreach ($this->items as $permission)
        {
            if(!array_key_exists($permission->object, $index)) {
                $index[$permission->object] = [];
            }
            $index[$permission->object][$permission->ability] = $permission;
        }
        return $this->index = $index;
    }
}