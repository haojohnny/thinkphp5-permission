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
}
