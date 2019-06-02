<?php

namespace Haojohnny\Permission\Exceptions;

use think\Exception;

class PermissionDoesNotExist extends Exception
{
    /**
     * @param $permissionName
     * @return PermissionDoesNotExist
     */
    public static function message($permissionName)
    {
        return new static("There is no permission `{$permissionName}`.");
    }
}