<?php

namespace Haojohnny\Permission\Exceptions;

use think\Exception;

class RoleAlreadyExists extends Exception
{
    /**
     * @param $role
     * @return RoleAlreadyExists
     */
    public static function message($role)
    {
        return new static("A role `{$role}` already exists.");
    }
}
