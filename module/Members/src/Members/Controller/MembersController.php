<?php

namespace Members\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Account\Model\Account;
use Account\Model\Role;

class MembersController extends AbstractActionController
{
    protected $accountTable;

    public function indexAction()
    {
        $members = $this->getAccountTable()->getMembers()->toArray();

        for ($i = 0; $i < count($members); $i++) {
            $members[$i]['role'] = Role::convertToRole((int) $members[$i]['role']);
        }

        return new ViewModel([
            'members' => $members,
        ]);
    }

    /**
     * @return array|NewsTable|object
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
