<?php

namespace Haojohnny\Permission\Exceptions;

use think\Exception;

class RoleNotFoundException extends Exception
{
    /**
     * @param $role
     * @return RoleNotFoundException
     */
    public static function message($role)
    {
        return new static("There is no role `{$role}`.");
    }
}
