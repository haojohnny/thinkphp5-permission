<?php

namespace Haojohnny\Permission\Exceptions;

use think\Exception;

class PermissionNotFound extends Exception
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