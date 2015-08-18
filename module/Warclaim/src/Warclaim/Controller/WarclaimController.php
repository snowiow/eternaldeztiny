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
        $session = new \Zend\Session\Container('user');
        if (!$session || $session->role < \Account\Model\Role::CO) {
            return $this->redirect()->toRoute('account', ['action' => 'noright']);
        }

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
            $size = $request->getPost()['size'];
            $form = new CreateForm($size);
            $form->setInputFilter($warclaim->getCreateInputFilter($size));
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $warclaim->exchangeArray($form->getData());
                $this->getWarclaimTable()->saveWarclaim($warclaim);

                return $this->redirect()->toRoute('warclaim', ['action' => 'current']);
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

        $session = new \Zend\Session\Container('user');
        if (!$session || $session->role < \Account\Model\Role::CO) {
            return $this->redirect()->toRoute('account', ['action' => 'noright']);
        }
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
        $session  = new \Zend\Session\Container('user');
        $warclaim = $this->getWarclaimTable()->getCurrentWar();
        if (!$warclaim || !$session || $session->role < \Account\Model\Role::MEMBER) {
            return $this->redirect()->toRoute('account', ['action' => 'noright']);
        }

        $members = $this->getAccountTable()->getMembers();
        $form    = new CurrentForm($warclaim->getSize(), $session->role);
        $form->setData($warclaim->getArrayCopy());

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($warclaim->getCurrentInputFilter($warclaim->getSize()));
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $warclaim->exchangeArray($form->getData());
                //Validate if info is filled out if a cleanup was set
                $errors = [];
                for ($i = 0; $i < $warclaim->getSize(); $i++) {
                    if ($warclaim->getCleanup()[$i] !== '' && $warclaim->getInfo()[$i] === '') {
                        $errors[$i . 'i'] = 'You have to give an info, when you attack.';
                    }
                }
                if ($errors) {
                    return [
                        'form'     => $form,
                        'warclaim' => $warclaim,
                        'errors'   => $errors,
                        'members'  => $members,
                        'user'     => $session,
                    ];
                }
                $this->getWarclaimTable()->saveWarclaim($warclaim);

                return $this->redirect()->toRoute('warclaim', ['action' => 'current']);
            } else {
                $errors = $form->getMessages();

                return [
                    'form'     => $form,
                    'warclaim' => $warclaim,
                    'errors'   => $errors,
                    'members'  => $members,
                    'user'     => $session,
                ];
            }
        }
        return ['form' => $form, 'warclaim' => $warclaim, 'user' => $session, 'members' => $members];
    }

    public function nowarAction()
    {
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
