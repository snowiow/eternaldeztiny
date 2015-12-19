<?php

namespace Warstatus\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Account\Model\Role;
use Account\Service\PermissionChecker;
use Account\Model\Account;
use Account\Model\AccountTable;

use Warstatus\Model\WarstatusTable;
use Warstatus\Model\Warstatus;
use Warstatus\Form\WarstatusForm;

class WarstatusController extends AbstractActionController
{
    /**
     * @var WarstatusTable
     */
    private $warstatusTable;

    /**
     * @var AccountTable
     */
    private $accountTable;

    public function editAction()
    {
        if (!PermissionChecker::check(Role::MEMBER)) {
            return $this->redirect()->toRoute('account',
                [
                    'action' => 'noright',
                ]
            );
        }

        $session   = new \Zend\Session\Container('user');
        $warstatus = null;
        try {
            $warstatus = $this
                ->getWarstatusTable()
                ->getWarstatus((int) $session->id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('news');
        }

        if (!$warstatus) {
            $warstatus = new Warstatus($session->id);
            $warstatus->setOptedOutDate((new \DateTime())->format('Y-m-d'));
        }
        $form = new WarstatusForm();

        //Prepare date for HTML 5 input
        $date = new \DateTime($warstatus->getOptedInDate());
        $warstatus->setOptedInDate($date->format('Y-m-d'));

        $form->bind($warstatus);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($warstatus->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getWarstatusTable()->saveWarstatus($form->getData());

                return $this->redirect()->toRoute(
                    'account',
                    ['action' => 'profile']
                );
            } else {
                $errors = $form->getMessages();
                return ['form' => $form, 'errors' => $errors];
            }
        }
        return ['form' => $form];
    }

    public function indexAction()
    {
        $acc_arr    = [];
        $out_of_war = 0;
        $accounts   = $this->getAccountTable()->getMembersWithWarstatus();

        foreach ($accounts as $account) {
            $acc_arr[] = $account;
            if ($account->getWarstatus()->getDurationLeft() > 0) {
                $out_of_war++;
            }
        }

        usort($acc_arr, function ($acc1, $acc2) {
            $duration1 = $acc1->getWarstatus()->getDurationLeft();
            $duration2 = $acc2->getWarstatus()->getDurationLeft();
            if ($duration1 === $duration2) {
                return 0;
            }
            if ($duration1 === 0) {
                return 1;
            }
            if ($duration2 === 0) {
                return -1;
            }
            return ($duration1 < $duration2) ? -1 : 1;
        });
        return ['accounts' => $acc_arr, 'out_of_war' => $out_of_war];
    }

    private function getWarstatusTable()
    {
        if (!$this->warstatusTable) {
            $sm                   = $this->getServiceLocator();
            $this->warstatusTable = $sm->get('Warstatus\Model\WarstatusTable');
        }
        return $this->warstatusTable;
    }

    /**
     * @return AccountTable
     */
    private function getAccountTable()
    {
        if (!$this->accountTable) {
            $sm                 = $this->getServiceLocator();
            $this->accountTable = $sm->get('Account\Model\AccountTable');
        }
        return $this->accountTable;
    }
}
