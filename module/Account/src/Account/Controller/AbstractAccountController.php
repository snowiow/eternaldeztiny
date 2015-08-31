<?php

namespace Account\Controller;

use Zend\Mvc\Controller\AbstractActionController;

abstract class AbstractAccountController extends AbstractActionController
{

    /**
     * @var AccountTable
     */
    protected $accountTable;

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
