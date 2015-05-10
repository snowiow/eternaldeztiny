<?php

namespace Account\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Captcha\AdapterInterface as CaptchaAdapter;
use Zend\Captcha\Figlet;

use Account\Form\RegisterForm;
use Account\Model\Account;
use Account\Model\AccountTable;

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
                $this->getAccountTable()->saveAccount($account);

                return $this->redirect()->toRoute('news');
            } else {
                $errors = $form->getMessages();
                return ['form' => $form, 'errors' => $errors];
            }
        }

        return ['form' => $form];
    }

    public function loginAction()
    {
    }

    public function getAccountTable()
    {
        if (!$this->accountTable) {
            $sm = $this->getServiceLocator();
            $this->accountTable = $sm->get('Account\Model\AccountTable');
        }

        return $this->accountTable;
    }
}
