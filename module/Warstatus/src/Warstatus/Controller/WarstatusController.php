<?php

namespace Warstatus\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Account\Model\Role;
use Account\Service\PermissionChecker;
use Warstatus\Model\WarstatusTable;
use Warstatus\Model\Warstatus;
use Warstatus\Form\WarstatusForm;

class WarstatusController extends AbstractActionController
{
    /**
     * @var WarstatusTable
     */
    private $warstatusTable;

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
            $warstatus->setOptedOutDate((new \DateTime())->format('Y-m-d H:i'));
        }
        $form = new WarstatusForm();

        //Prepare date for HTML 5 input
        $date = new \DateTime($warstatus->getOptedInDate());
        $warstatus->setOptedInDate($date->format('Y-m-d\TH:i'));

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

    }

    private function getWarstatusTable()
    {
        if (!$this->warstatusTable) {
            $sm                   = $this->getServiceLocator();
            $this->warstatusTable = $sm->get('Warstatus\Model\WarstatusTable');
        }
        return $this->warstatusTable;
    }
}
