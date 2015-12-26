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
        $account   = $this->getAccountTable()->getAccount($session->id);
        $warstatus = null;
        try {
            $warstatus = $this
                ->getWarstatusTable()
                ->getWarstatus((int) $account->getId());
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('news');
        }

        if (!$warstatus) {
            $warstatus = new Warstatus($session->id);
            $warstatus->setOptedOutDate((new \DateTime())->format('Y-m-d'));
            $warstatus->setOptedOutDateMini((new \DateTime())->format('Y-m-d'));
        }
        $form = new WarstatusForm();

        //Prepare date for HTML 5 input
        $date      = new \DateTime($warstatus->getOptedInDate());
        $date_mini = new \DateTime($warstatus->getOptedInDateMini());
        $warstatus->setOptedInDate($date->format('Y-m-d'));
        $warstatus->setOptedInDateMini($date_mini->format('Y-m-d'));

        $form->bind($warstatus);

        $has_mini = $account->getMini() ? true : false;
        $request  = $this->getRequest();
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
                return ['form' => $form, 'errors' => $errors, 'has_mini' => $has_mini];
            }
        }
        return ['form' => $form, 'has_mini' => $has_mini];
    }

    public function indexAction()
    {
        $acc_arr  = [];
        $in_war   = 0;
        $accounts = $this->getAccountTable()->getMembersWithWarstatus();

        foreach ($accounts as $account) {
            $warstatus      = $account->getWarstatus();
            $ws_arr         = $warstatus->getWarstatusAsArray();
            $ws_arr['name'] = $account->getName();
            $acc_arr[]      = $ws_arr;
            if ($account->getMini()) {
                $ws_arr         = $warstatus->getWarstatusAsArrayMini();
                $ws_arr['name'] = $account->getMini();
                $acc_arr[]      = $ws_arr;
                if ($warstatus->getDurationLeftMini() < 1) {
                    $in_war++;
                }
            }
            if ($warstatus->getDurationLeft() < 1) {
                $in_war++;
            }
        }

        usort($acc_arr, function ($acc1, $acc2) {
            $duration1 = $acc1['duration'];
            $duration2 = $acc2['duration'];
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
        return ['accounts' => $acc_arr, 'in_war' => $in_war];
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
