<?php

namespace Clan\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Clan\Form\ClanForm;

class ClanController extends AbstractActionController
{
    private $clanTable;

    public function addAction()
    {
        $form = new ClanForm();

        return ['form' => $form];
    }

    private function getClanTable()
    {
        if (!$this->ClanTable) {
            $sm              = $this->getServiceLocator();
            $this->clanTable = $sm->get('Clan\Model\ClanTable');
        }
        return $this->clanTable;
    }
}
