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
        $captcha = new Figlet([
            'name' => 'captcha',
            'wordLen' => 6,
            'timeout' => 300,
        ]);
        $id = $captcha->generate();
        $form = new RegisterForm($captcha);
        $form->get('submit')->setValue('Register');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $account = new Account();
            $form->setInputFilter($account->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $account->exchangeArray($form->getData());
                $this->getAccountTable()->saveAccount($account);

                return $this->redirect()->toRoute('account');
            }
        }
        return array('form' => $form);
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
