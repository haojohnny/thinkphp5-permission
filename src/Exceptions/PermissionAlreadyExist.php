<?php

namespace Haojohnny\Permission\Exceptions;

use think\Exception;

class PermissionAlreadyExist extends Exception
{
    /**
     * @param $permissionName
     * @return PermissionAlreadyExist
     */
    public static function message($permissionName)
    {
        return new static("A `{$permissionName}` permission already exists.");
    }
}