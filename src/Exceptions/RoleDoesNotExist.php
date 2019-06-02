<?php

namespace Haojohnny\Permission\Exceptions;

use think\Exception;

class RoleDoesNotExist extends Exception
{
    /**
     * @param $role
     * @return RoleDoesNotExist
     */
    public static function message($role)
    {
        return new static("There is no role `{$role}`.");
    }
}
