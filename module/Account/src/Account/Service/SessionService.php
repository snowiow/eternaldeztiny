<?php

namespace Account\Service;

use Zend\Session\Container;

use Account\Model\Account;

class SessionService
{
    /**
     * Creates a user session in the user namespace.
     * id, role, name, avatar and registered are accessable afterwards.
     * @param \Account\Model\Account $account
     */
    public static function createUserSession(Account $account)
    {
        $session             = new Container('user');
        $session->id         = $account->getId();
        $session->role       = $account->getRole();
        $session->name       = $account->getName();
        $session->avatar     = $account->getAvatar();
        $session->registered = $account->getDateRegistered();
    }

    /**
     * Destroys the user session in the user namespace.
     */
    public static function destroyUserSession()
    {
        $session = new Container('user');
        if (isset($session->id)) {
            unset($session->id);
        }

        if (isset($session->role)) {
            unset($session->role);
        }

        if (isset($session->name)) {
            unset($session->name);
        }

        if (isset($session->avatar)) {
            unset($session->avatar);
        }

        if (isset($session->registered)) {
            unset($session->registered);
        }
    }
}
