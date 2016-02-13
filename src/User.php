<?php

namespace Birdmin;

use Birdmin\Core\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Birdmin\Collections\PermissionCollection;

class User extends Model
    implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    protected $table = 'users';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'website',
        'position',
        'affiliation',
        'personal_info',
    ];

    protected $searchable = ['id','first_name','last_name','email','position'];

    /**
     * The attributes excluded from the model's JSON form.
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    protected $dates = ['updated_at','created_at'];

    protected static $ownerKey = "id";

    /**
     * Return associated roles.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles ()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if a user has a role.
     * @param $role Role|string role name
     * @return mixed
     */
    public function hasRole ($role)
    {
        if(is_string($role)) {
            $role = Role::getByName($role);
        }
        return $this->roles->contains($role);
    }

    /**
     * Assign a role to a user.
     * @param string|int|Role $role
     * @return mixed
     * @throws \Exception
     */
    public function assignRole ($role)
    {

        if (is_string($role)) {
            $role = Role::getByName($role);
        }
        if (!$role || empty($role)) {
            throw new \Exception('Role missing and cannot be assigned to user');
        }
        if ($this->hasRole($role)) {
            return true;
        }
        return $this->roles()->attach($role);
    }

    /**
     * Return this user's combined permissions from all roles.
     * @return PermissionCollection
     */
    public function permissions()
    {
        // Use the indexed value rather than doing a new query each time.
        if (!empty($this->permissions)) {
            return $this->permissions;
        }
        $permissions = new PermissionCollection();

        foreach ($this->roles as $role) {
            $permissions->addRole($role);
        }
        return $this->permissions = $permissions;
    }

    /**
     * Return the full name of the user.
     * @param bool|false $reverse - last name, first name
     * @return string
     */
    public function fullName($reverse=false)
    {
        return $reverse & !empty($this->last_name) ? $this->last_name.", ".$this->first_name :
            $this->first_name." ".$this->last_name;
    }

    /**
     * Return the user's gravatar image.
     * @param int $s
     * @return string
     */
    public function gravatar($s=80)
    {
        if (!$s || $s<=0) $s = 80;
        $baseUrl = "http://www.gravatar.com/avatar/".md5(strtolower($this->email))."?";
        return $baseUrl.http_build_query(['s'=>$s, 'r'=>'g', 'd'=>'mm']);
    }

    /**
     * Return an image tag of this user.
     * @param null|string $size
     * @param null|string|array $classes
     * @param array $attrs
     * @return string
     */
    public function img($size=null,$classes=null,$attrs=[])
    {
        switch ($size) {
            case "sm" : $size = 80; break;
            case "md" : $size = 300; break;
            case "lg" : $size = 600; break;
            case null : $size = 800; break;
            default :   $size = 800; break;
        }
        $src = $this->gravatar($size);
        $attr = [
            'src'=>$src,
            'class'=>$classes,
            'alt'=>$this->fullName()." Image"
        ];

        return attributize(array_merge($attr,$attrs), 'img');
    }

    /**
     * The User id is the owner id.
     * @return int
     */
    public function ownerId()
    {
        return $this->id;
    }
}
