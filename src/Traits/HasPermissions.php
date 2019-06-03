<?php

namespace Haojohnny\Permission\Traits;

use think\model\relation\belongsToMany;
use think\model\Collection;
use Haojohnny\Permission\Models\Permissions;
use Haojohnny\Permission\Exceptions\PermissionNotFound;

trait HasPermissions
{
    /**
     * @return Permissions
     */
    public function getPermissionInstance(): Permissions
    {
        return app(\Haojohnny\Permission\Models\Permissions::class);
    }

    /**
     * @return belongsToMany
     */
    public function permissions(): belongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permissions'),
            config('permission.tables.model_has_permissions'),
            'permission_id',
            'model_id'
        );
    }

    /**
     * 为use HasPermissions或use HasRoles的model添加权限
     * @param mixed ...$permissions
     * @return $this
     */
    public function givePermission(...$permissions)
    {
        $permissionIds = (new Collection(array_flatten($permissions)))
            ->each(function ($name) {
                return $this->getPermissionInstance()->findOrCreate($name);
            })
            ->filter(function ($permission) {
                return !$this->hasDirectPermission($permission);
            })
            ->column('id');

        if (!empty($permissionIds)) {
            $this->permissions()->saveAll($permissionIds);
        }

        return $this;
    }

    /**
     * 为use HasPermissions或use HasRoles的model撤销权限
     * @param mixed ...$permissions
     * @return $this
     */
    public function revokePermission(...$permissions)
    {
        $permissionIds = $this->permissions->where('name', 'in', array_flatten($permissions))->column('id');

        if (!empty($permissionIds)) {
            $this->permissions()->detach($permissionIds);
        }

        return $this;
    }

    /**
     * 检查use HasPermissions或use HasRoles的model是否拥有权限
     * @param $permission
     * @param bool $checkRole 是否检查该权限角色
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function hasPermission($permission, $checkRole = true): bool
    {
        try {
            $permission = $this->getStoredPermission($permission);
        } catch (PermissionNotFound $exception) {
            return false;
        }

        return $this->hasDirectPermission($permission) || ($checkRole && $this->hasRole($permission->roles));
    }

    /**
     * 是否拥有直接权限
     * @param Permissions $permission
     * @return bool
     */
    protected function hasDirectPermission(Permissions $permission)
    {
        return !$this->permissions->where('id', $permission->id)->isEmpty();
    }

    /**
     * 是否拥有该权限的角色
     * @param Permissions $permission
     * @return bool
     */
    protected function hasPermissionViaRole(Permissions $permission): bool
    {
        return $this->hasAnyRole($permission->roles);
    }

    /**
     * 获取use HasPermissions或use HasRoles的model所有直接权限
     * @return false|\think\model\Collection
     * @throws \think\Exception\DbException
     */
    public function getDirectPermissions(): Collection
    {
        return $this->permissions()->all();
    }

    /**
     * @param $permission
     * @return array|false|mixed|\PDOStatement|string|\think\Model|null
     * @throws PermissionDoesNotExist
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getStoredPermission($permission)
    {
        if (is_string($permission)) {
            $permission = $this->getPermissionInstance()->findByName($permission);
        }

        if (is_numeric($permission)) {
            $permission = $this->getPermissionInstance()->findById($permission);
        }

        if (! $permission instanceof Permissions) {
            throw new PermissionNotFound;
        }

        return $permission;
    }
}
