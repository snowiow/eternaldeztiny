<?php

namespace Warclaim\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Warclaim\Form\CreateForm;
use Warclaim\Form\CurrentForm;
use Warclaim\Form\PrecautionsForm;
use Warclaim\Model\Warclaim;
use Warclaim\Model\WarclaimTable;

class WarclaimController extends AbstractActionController
{
    private $warclaimTable;
    private $accountTable;

    public function createAction()
    {
        $size     = (int) $this->params()->fromRoute('size', 10);
        $opponent = $this->params()->fromRoute('opponent', '');
        $form     = new CreateForm($size);
        $warclaim = new Warclaim();
        $warclaim->setSize($size);
        $warclaim->setOpponent($opponent);
        $form->bind($warclaim);
        $request = $this->getRequest();

        $members = $this->getAccountTable()->getMembers();
        if ($request->isPost()) {
            $form->setInputFilter($warclaim->getCreateInputFilter($size));
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $warclaim = $form->getData();
                $this->getWarclaimTable()->saveWarclaim($warclaim);

                return $this->redirect()->toRoute('news');
            } else {
                $errors = $form->getMessages();

                return new ViewModel([
                    'form'     => $form,
                    'size'     => $size,
                    'errors'   => $errors,
                    'members'  => $members,
                    'opponent' => $opponent,
                ]);
            }
        }
        return new ViewModel(
            [
                'form'     => $form,
                'size'     => $size,
                'members'  => $members,
                'opponent' => $opponent,
            ]
        );
    }

    public function precautionsAction()
    {
        $form    = new PrecautionsForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $warclaim = new Warclaim();
            $form->setInputFilter($warclaim->getPrecautionsInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data     = $form->getData();
                $size     = (int) $data['size'];
                $opponent = $data['opponent'];
                $this->redirect()->toRoute('warclaim', [
                    'action'   => 'create',
                    'size'     => $size,
                    'opponent' => $opponent,
                ]);
            } else {
                $errors = $form->getMessages();
                return ['form' => $form, 'errors' => $errors];
            }
        }
        return ['form' => $form];
    }

    public function currentAction()
    {
        $warclaim = $this->getWarclaimTable()->getCurrentWar();
        if (!$warclaim) {
            $this->redirect()->toRoute('news');
        }

        $form = new CurrentForm($warclaim->getSize());
        $form->bind($warclaim);

        return ['form' => $form, 'warclaim' => $warclaim];
    }

    /**
     * @return array|ApplicationTable|object
     */
    public function getWarclaimTable()
    {
        if (!$this->warclaimTable) {
            $sm                  = $this->getServiceLocator();
            $this->warclaimTable = $sm->get('Warclaim\Model\WarclaimTable');
        }

        return $this->warclaimTable;
    }

    /**
     * @return array|AccountTable|object
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
