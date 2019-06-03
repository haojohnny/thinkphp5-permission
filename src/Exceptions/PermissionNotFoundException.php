<?php

namespace Haojohnny\Permission\Exceptions;

use think\Exception;

class PermissionNotFoundException extends Exception
{
    /**
     * @param $permissionName
     * @return PermissionNotFoundException
     */
    public static function message($permissionName)
    {
        return new static("There is no permission `{$permissionName}`.");
    }
}