<?php

namespace Haojohnny\Permission\Traits;

use think\model\relation\belongsToMany;
use think\model\Collection;
use Haojohnny\Permission\Models\Permissions;
use Haojohnny\Permission\Exceptions\PermissionDoesNotExist;

trait HasPermissions
{
    /**
     * @return Permissions
     */
    public function getPermissionInstance(): Permissions
    {
        return app((config('permission.models.permissions')));
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
     * 为model添加权限
     * @param mixed ...$permissions
     * @return $this
     */
    public function giveDirectPermission(...$permissions)
    {
        $permissionIds = (new Collection(array_flatten($permissions)))
            ->each(function ($name) {
                return $this->findOrCreate($name);
            })
            ->filter(function ($permission) {
                return ! $this->hasDirectPermission($permission);
            })
            ->column('id');

        if (! empty($permissionIds)) {
            $this->permissions()->saveAll($permissionIds);
        }

        return $this;
    }

    /**
     * 为model撤销权限
     * @param mixed ...$permissions
     * @return $this
     */
    public function revokePermission(...$permissions)
    {
        $permissionIds = $this->permissions->where('name', 'in', array_flatten($permissions))->column('id');

        if (! empty($permissionIds)) {
            $this->permissions()->detach($permissionIds);
        }

        return $this;
    }

    /**
     * 是否拥有权限
     * @param $permission
     * @param bool $checkRole 是否检查角色权限
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function hasPermission($permission, $checkRole = true): bool
    {
        try {
            $permission = $this->getStoredPermission($permission);
        } catch (PermissionDoesNotExist $exception) {
            return false;
        }

        return $this->hasDirectPermission($permission) || ($checkRole && $this->hasPermissionViaRole($permission));
    }

    /**
     * 是否拥有直接权限
     * @param Permissions $permission
     * @return bool
     */
    protected function hasDirectPermission(Permissions $permission)
    {
        return ! $this->permissions->where('id', $permission->id)->isEmpty();
    }

    /**
     * 是否拥有该权限的角色
     * @param Permissions $permission
     * @return bool
     */
    protected function hasPermissionViaRole(Permissions $permission): bool
    {
        return $this->hasRole($permission->roles);
    }

    /**
     * 获取Model所有直接权限
     * @return false|belongsToMany[]
     * @throws \think\Exception\DbException
     */
    public function getDirectPermissions()
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
            $permission = $this->findByName($permission);
        }

        if (is_numeric($permission)) {
            $permission = $this->findById($permission);
        }

        if (! $permission instanceof Permissions) {
            throw new PermissionDoesNotExist;
        }

        return $permission;
    }

    /**
     * @param string $permission
     * @return array|false|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function findByName(string $permission)
    {
        return $this->getPermissionInstance()->where('name', $permission)->find();
    }

    /**
     * @param int $id
     * @return mixed
     */
    protected function findById(int $id)
    {
        return $this->getPermissionInstance()::get($id);
    }

    /**
     * @param $name
     * @return array|false|Permissions|\PDOStatement|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function findOrCreate($name)
    {
        $permission = $this->findByName($name);

        if (! $permission) {
            $permission = $this->getPermissionInstance()::create(['name' => $name]);
        }

        return $permission;
    }
}
