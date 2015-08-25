<?php

namespace Account\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Account\Model\Role;
use Account\Service\PermissionChecker;

class AdminController extends AbstractActionController
{
    protected $accountTable;

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

    /**
     * Retrieve the accountTable
     *
     * @return AccountTable
     */
    public function getAccountTable()
    {
        if (!$this->accountTable) {
            $sm                 = $this->getServiceLocator();
            $this->accountTable = $sm->get('Account\Model\AccountTable');
        }

        return $this->accountTable;
    }

}
