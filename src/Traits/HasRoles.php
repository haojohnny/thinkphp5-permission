<?php

namespace Haojohnny\Permission\Traits;

use Haojohnny\Permission\Models\Roles;
use Haojohnny\Permission\Exceptions\RoleDoesNotExist;
use think\model\relation\belongsToMany;
use think\model\Collection;

trait HasRoles
{
    use HasPermissions;

    protected $rolesInstance;

    /**
     * @return Roles
     */
    public function getRolesInstance(): Roles
    {
        return app((config('permission.models.roles')));
    }
    
    // 多用户 <----> 多角色
    public function roles(): belongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.roles'),
            config('permission.tables.model_has_roles'),
            'model_id',
            'role_id'
        );
    }

    /**
     * @param $role
     * @return bool
     */
    public function hasRole($role): bool
    {
        try {
            $role = $this->getStoredRole($role);
        } catch (RoleDoesNotExist $exception) {
            return false;
        }

        return ! $this->roles->intersect($role)->isEmpty();
    }

    /**
     * 获取Model所有角色
     * @return false|belongsToMany[]|\think\model\Collection
     * @throws \think\Exception\DbException
     */
    public function getRoles(): Collection
    {
        return $this->roles()->all();
    }

    /**
     * @param $role
     * @return Roles
     * @throws RoleDoesNotExist
     */
    public function getStoredRole($role): Roles
    {
        if (is_string($role)) {
            $role = $this->roles->where('name', $role);
        }

        if (is_numeric($role)) {
            $role = $this->roles->get($role);
        }

        if (! $role instanceof Roles) {
            throw new RoleDoesNotExist;
        }

        return $role;
    }
}
