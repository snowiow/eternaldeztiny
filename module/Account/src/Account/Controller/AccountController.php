<?php

namespace Account\Controller;

use Zend\Mvc\Controller\AbstractActionController;

use Account\Form\RegisterForm;
use Account\Model\Account;
use Account\Model\AccountTable;
use AppMail\Service\AppMailServiceInterface;

class AccountController extends AbstractActionController
{
    /**
     * @var AccountTable
     */
    protected $accountTable;

    /**
     * @var \AppMail\Service\AppMailServiceInterface
     */
    protected $appMailService;

    /**
     * @param \AppMail\Service\AppMailServiceInterface $appMailService
     */
    public function __construct(AppMailServiceInterface $appMailService)
    {
        $this->appMailService = $appMailService;
    }

    public function indexAction()
    {
        print_r("test");
    }

    /**
     * the registration action- Either returns the registration page or adds an account to the db and sends out an email.
     * @return array|\Zend\Http\Response
     */
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
                if (!$this->getAccountTable()->getAccountByName($account->getName())) {
                    if (!$this->getAccountTable()->getAccountByMail($account->getEmail())) {
                        $this->getAccountTable()->saveAccount($account);
                        $this->sendConfirmationMail($account);
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

    public function loginAction()
    {
    }

    public function registerSuccessAction()
    {
    }

    /**
     * Retrieve the accountTable
     * @return AccountTable
     */
    public function getAccountTable()
    {
        if (!$this->accountTable) {
            $sm = $this->getServiceLocator();
            $this->accountTable = $sm->get('Account\Model\AccountTable');
        }

        return $this->accountTable;
    }

    /**
     * Sends out a confirmation mail to the registered account
     *
     * @param Account $account
     */
    private function sendConfirmationMail($account)
    {
        $mailText = "Congratulations " . $account->getName() . ", you registered at Eternal Deztiny. To complete your registration, ";
        $mailText = $mailText . "follow the link:\ned.com/account/registersuccess/" . $account->getUserHash();

        $this->appMailService->sendMail($account->getEmail(), 'Your registration at Eternal Deztiny', $mailText);
    }
}
