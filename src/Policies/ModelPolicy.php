<?php

namespace Birdmin\Policies;

use Birdmin\Core\Extender;
use Birdmin\Permission;
use Birdmin\User;
use Birdmin\Core\Model;
use Birdmin\Collections\PermissionCollection;

class ModelPolicy
{
    /**
     * The permission index.
     * @var PermissionCollection
     */
    protected $permissions;

    /**
     * The requested model class name.
     * @var string
     */
    protected $class;

    /**
     * If the current model class is a managed class.
     * For example, if an object has a user_id column.
     * @var bool
     */
    protected $manager;

    /**
     * ModelPolicy constructor.
     */
    public function __construct(Extender $extender)
    {
        $this->permissions = Permission::index();
        $this->extender = $extender;
    }

    /**
     * Fired before the policies get checked.
     * @param User $user
     * @param $ability
     * @param $model Model|string
     * @return bool
     */
    public function before (User $user, $ability, $model)
    {
        $this->class = is_object($model) ? get_class($model) : $model;

        // Does the user have the Super User role?
        if ($user->hasRole('Super User')) {
            return true;
        }
        // Does the particular class have a management ability?
        $this->manager = $this->permissions->exists('manage', $this->class);

        // Does the permission exist in the index? If not, grant by default.
        if (! $this->permissions->exists($ability, $this->class)) {
            return true;
        }

        // Is this a managed class and does the user have the managed permissions?
        if ($this->manager) {
            return $this->manage($user,$model,$ability);
        }
    }

    /**
     * Can view the object.
     * @param User $user
     * @param Model $model
     * @return bool
     */
    public function view (User $user, $model)
    {
        return $user->permissions()->exists('view', $model);
    }

    /**
     * Can edit the object.
     * @param User $user
     * @param Model $model
     * @return bool
     */
    public function edit (User $user, $model)
    {
        return $user->permissions()->exists('edit', $model);
    }

    /**
     * Can create a new object.
     * @param User $user
     * @param Model $model
     * @return bool
     */
    public function create (User $user, $model)
    {
        return $user->permissions()->exists('create', $model);
    }

    /**
     * Can delete an object.
     * @param User $user
     * @param Model $model
     * @return bool
     */
    public function delete (User $user, $model)
    {
        return $user->permissions()->exists('delete', $model);
    }

    /**
     * Can manage other objects.
     * @param User $user
     * @param Model $model
     * @return bool
     */
    public function manage (User $user, $model, $ability=null)
    {
        // If the user has the manage permission, let them do whatever.
        if ($user->permissions()->exists('manage', $model)) {
            return true;
        }
        // if the model is just the class and the ability is just to view.
        // We don't have an object to check.
        if ($ability=='view') {
            return $this->view($user,$model);
        }
        // We already found out that the user doesn't have the manage permission for this class.
        if (is_string($model)) {
            return false;
        }
        // Otherwise, check the id of the object against the owner's user id.
        return $user->id === $model->ownerId();
    }
}
