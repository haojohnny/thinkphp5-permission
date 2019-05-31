<?php

namespace Haojohnny\Permission\Models;

use Haojohnny\Permission\Traits\HasPermissions;
use think\Model;
use think\model\relation\BelongsToMany;

class Roles extends Model
{
    use HasPermissions;

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
}
