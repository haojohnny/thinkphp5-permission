<?php

namespace Haojohnny\Permission\Models;

use Haojohnny\Permission\Traits\HasRoles;
use think\Model;
use think\model\relation\BelongsToMany;

class Roles extends Model
{
    use HasRoles;

    public function initialize()
    {
        $this->table(config('permission.tables.roles'));
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permissions'),
            config('permission.tables.role_has_permissions'),
            'permission_id',
            'role_id'
        );
    }

    /**
     * @param string $permission
     * @return array|false|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findByName(string $permission)
    {
        return self::where('name', $permission)->find();
    }

    /**
     * @param int $id
     * @return mixed
     */
    public static function findById(int $id)
    {
        return self::get($id);
    }

    /**
     * @param $name
     * @return array|false|Roles|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function findOrCreate($name)
    {
        $role = self::findByName($name);

        if (!$role) {
            $role = self::create(['name' => $name]);
        }

        return $role;
    }
}
