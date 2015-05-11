<?php

namespace Account\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Account\Form\RegisterForm;
use Account\Model\Account;
use ZendTest\XmlRpc\Server\Exception;

class AccountController extends AbstractActionController
{
    protected $accountTable;

    public function indexAction()
    {
        print_r("test");
    }

    public function registerAction()
    {
        $form = new RegisterForm();
        $form->get('submit')->setValue('Register');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $account = new Account();
            $form->setInputFilter($account->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $account->exchangeArray($form->getData());
                if (!$this->getAccountTable()->getAccountByName($account->name)) {
                    if (!$this->getAccountTable()->getAccountByMail($account->email)) {
                        $this->getAccountTable()->saveAccount($account);
                    } else {
                        return ['form' => $form, 'errors' => $errors = ['email' => 'email_taken']];
                    }
                } else {
                    return ['form' => $form, 'errors' => $errors = ['name' => 'name_taken']];
                }

                return $this->redirect()->toRoute('account', [
                    'controller' => 'account',
                    'action'     => 'registersuccess',
                ]);
            } else {
                $errors = $form->getMessages();

                return ['form' => $form, 'errors' => $errors];
            }
        }

        return ['form' => $form];
    }

    public
    function loginAction()
    {
    }

    public
    function registerSuccessAction()
    {
    }

    public
    function getAccountTable()
    {
        if (!$this->accountTable) {
            $sm = $this->getServiceLocator();
            $this->accountTable = $sm->get('Account\Model\AccountTable');
        }

        return $this->accountTable;
    }
}
