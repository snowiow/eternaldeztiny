<?php

namespace Warclaim\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Warclaim\Form\WarclaimForm;
use Warclaim\Model\Warclaim;
use Warclaim\Model\WarclaimTable;

class WarclaimController extends AbstractActionController
{
    private $warclaimTable;
    private $accountTable;

    public function createAction()
    {
        $form    = new WarclaimForm();
        $request = $this->getRequest();

        $members = $this->getAccountTable()->getMembers();
        $size    = $request->getPost()['select'];
        if ($request->isPost()) {
            if (array_key_exists('select', $request->getPost())) {
                return new ViewModel([
                    'form'    => $form,
                    'size'    => $size,
                    'members' => $members,
                ]);
            }
            $warclaim = new Warclaim();
            $form->setInputFilter($warclaim->getInputFilter($size));
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $warclaim->exchangeArray($form->getData());
                $this->getWarclaimTable()->saveWarclaim($warclaim);

                return $this->redirect()->toRoute('news');
            } else {
                $errors = $form->getMessages();

                return new ViewModel([
                    'form'    => $form,
                    'size'    => $size,
                    'errors'  => $errors,
                    'members' => $members,
                ]);
            }
        }
        return new ViewModel(
            [
                'form'    => $form,
                'size'    => 10,
                'members' => $members,
            ]
        );
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
