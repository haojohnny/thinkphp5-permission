<?php

namespace Haojohnny\Permission\Models;

use Haojohnny\Permission\Traits\HasRoles;
use think\Model;
use think\model\relation\BelongsToMany;

class Permissions extends Model
{
    use HasRoles;

    public function initialize()
    {
        $this->table(config('permission.tables.permissions'));
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.roles'),
            config('permission.tables.role_has_permissions'),
            'role_id',
            'permission_id'
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
     * @return array|false|Permissions|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function findOrCreate($name)
    {
        $permission = self::findByName($name);

        if (!$permission) {
            $permission = self::create(['name' => $name]);
        }

        return $permission;
    }
}
