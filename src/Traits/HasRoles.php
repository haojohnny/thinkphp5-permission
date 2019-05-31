<?php

namespace Haojohnny\Permission\Traits;

use Haojohnny\Permission\Models\Roles;
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
     * @param $roles
     * @return bool
     */
    public function hasRole($roles): bool
    {
        return ! $this->roles->intersect($roles)->isEmpty();
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
}
