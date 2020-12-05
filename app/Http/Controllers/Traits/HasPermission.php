<?php

namespace App\Http\Controllers\Traits;

// use Illuminate\Http\Request;
// use Intervention\Image\Facades\Image;
use App\User;
use Auth;

trait HasPermission
{

    // public function FunctionName(Type $var = null)
    // {
    //     # code...
    // }

    public function roles()
    {
        return Auth::user()->roles;
    }

    public function permissions()
    {
        return Auth::user()->permissions;
    }

    public function hasRole(...$roles)
    {
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if ($this->roles()->contains('slug', $role)) {
                    return true;
                }
            }

            return false;
        } else {
            if (count($this->roles()) > 0) {
                return true;
            }
            return false;
        }
    }

    public function can($permission)
    {
        return $this->hasPermission($permission);
    }

    protected function hasPermission($permission)
    {
        return (bool) $this->permissions()->where('slug', $permission)->count();
    }

    // Giving permissions
    public function givePermissionsTo(...$permissions)
    {
        $permissions = $this->getAllPermissions($permissions);

        if ($permissions === null) {
            return $this;
        }
        $this->permissions()->saveMany($permissions);
        return $this;
    }

    // Deleting a permission
    public function deletePermissions(...$permissions)
    {
        $permissions = $this->getAllPermissions($permissions);
        $this->permissions()->detach($permissions);
        return $this;
    }
}
