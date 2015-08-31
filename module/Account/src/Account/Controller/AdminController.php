<?php

namespace Account\Controller;

use Zend\View\Model\ViewModel;

use Account\Controller\AbstractAccountController;
use Account\Model\Role;
use Account\Service\PermissionChecker;

class AdminController extends AbstractAccountController
{
    public function setRolesAction()
    {
        if (!PermissionChecker::check(Role::CO)) {
            return $this->redirect()->toRoute('account', ['action' => 'noright']);
        }

        $users = $this->getAccountTable()->getUsersAndAbove();
        $roles = Role::getAllRoles();
        return new ViewModel([
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function updateroleAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data    = $request->getPost();
            $account = $this->getAccountTable()->getAccountBy(['id' => $data['id']]);
            $account->setRole($data['select']);
            $this->getAccountTable()->saveAccount($account);
        }
        $this->redirect()->toRoute('admin', ['action' => 'setroles']);
    }
}
