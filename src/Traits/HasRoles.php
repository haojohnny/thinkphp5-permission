<?php

namespace Haojohnny\Permission\Traits;

use Haojohnny\Permission\Models\Roles;
use Haojohnny\Permission\Exceptions\RoleDoesNotExist;
use think\model\relation\belongsToMany;
use think\model\Collection;

trait HasRoles
{
    use HasPermissions;

    /**
     * @return Roles
     */
    public function getRolesInstance(): Roles
    {
        return app(\Haojohnny\Permission\Models\Roles::class);
    }

    /**
     * @return belongsToMany
     */
    public function roles(): belongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.roles'),
            config('permission.tables.model_has_roles'),
            'role_id',
            'model_id'
        );
    }

    /**
     * 为use HasRoles的model分配角色
     * @param mixed ...$roles
     * @return $this
     */
    public function assignRole(...$roles)
    {
        $roleIds = (new Collection(array_flatten($roles)))
            ->each(function ($name) {
                return $this->getRolesInstance()->findOrCreate($name);
            })
            ->filter(function ($role) {
                return !$this->hasRole($role);
            })
            ->column('id');

        if (!empty($roleIds)) {
            $this->roles()->saveAll($roleIds);
        }

        return $this;
    }

    /**
     * 为use HasRoles的model撤销角色
     * @param mixed ...$roles
     * @return $this
     */
    public function revokeRole(...$roles)
    {
        $roleIds = $this->roles->where('name', 'in', array_flatten($roles))->column('id');

        if (!empty($roleIds)) {
            $this->roles()->detach($roleIds);
        }

        return $this;
    }

    /**
     * @param $roles
     * @return bool
     */
    public function hasRole($roles): bool
    {
        if (is_numeric($roles)) {
            return !$this->roles->where('id', $roles)->isEmpty();
        }

        if (is_string($roles)) {
            return !$this->roles->where('name', $roles)->isEmpty();
        }

        if ($roles instanceof Roles) {
            return !$this->roles('id', $roles->id)->isEmpty();
        }

        return !$this->roles->intersect($roles)->isEmpty();
    }

    /**
     * @param $roles
     * @return bool
     */
    public function hasAnyRole($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }

            return false;
        }

        return $this->hasRole($roles);
    }

    /**
     * 获取Model所有角色
     * @return false|\think\model\Collection
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
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getStoredRole($role): Roles
    {
        if (is_string($role)) {
            $role = $this->getRolesInstance()->findByName($role);
        }

        if (is_numeric($role)) {
            $role = $this->getRolesInstance()->findById($role);
        }

        if (! $role instanceof Roles) {
            throw new RoleDoesNotExist();
        }

        return $role;
    }
}
