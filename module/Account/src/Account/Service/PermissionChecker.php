<?php

namespace Account\Service;

use Zend\Session\Container;

use Account\Model\Role;

class PermissionChecker
{

    /**
     * Checks the user role against the given role.
     * @param Role|int $role the minimum role which should be allowed
     * @return bool if the check was successful, true will be returned, false otherwise
     */
    public static function check($role)
    {
        $session = new Container('user');
        if ($session->role < $role) {
            return false;
        }
        return true;
    }
}
