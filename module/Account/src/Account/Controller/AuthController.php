<?php

namespace Account\Controller;

use Account\Form\RegisterForm;
use Account\Form\LoginForm;
use Account\Model\Account;
use Account\Model\Role;
use Account\Service\SessionService;

use Application\Constants;
use AppMail\Service\AppMailServiceInterface;

interface AUTH_RESULT
{
    const SUCCESS           = 0;
    const WRONG_CREDENTIALS = 1;
    const NOT_FOUND         = 1 << 1;
    const NOT_CONFIRMED     = 1 << 2;
}

class AuthController extends AbstractAccountController
{

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

    /**
     * the registration action- Either returns the registration page or adds an account to the db and sends out an email.
     *
     * @return array|\Zend\Http\Response
     */
    public function registerAction()
    {
        $form = new RegisterForm();
        $form->get('submit')->setValue('Register');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $account = new Account();
            $form->setInputFilter($account->getRegisterInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $account->exchangeArray($form->getData());
                if (!$this->getAccountTable()->getAccountBy(['name' => $account->getName()])) {
                    if (!$this->getAccountTable()->getAccountBy(['email' => $account->getEmail()])) {
                        $account->setUserHash(hash('sha256', $account->getName()));
                        $this->getAccountTable()->saveAccount($account);
                        $this->sendConfirmationMail($account);
                    } else {
                        return ['form' => $form, 'errors' => $errors = ['email' => 'email_taken']];
                    }
                } else {
                    return ['form' => $form, 'errors' => $errors = ['name' => 'name_taken']];
                }

                return $this->redirect()->toRoute('auth', [
                    'action' => 'registersuccess',
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
        $form = new LoginForm();
        $form->get('submit')->setValue('Login');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $account = new Account();
            $form->setInputFilter($account->getLoginInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $account->exchangeArray($form->getData());
                $result = $this->authenticate($account);
                switch ($result) {
                    case AUTH_RESULT::NOT_FOUND:
                        return ['form' => $form, 'errors' => $errors = ['name' => 'name_not_available']];
                    case AUTH_RESULT::WRONG_CREDENTIALS:
                        return ['form' => $form, 'errors' => $errors = ['password' => 'wrong_password']];
                    case AUTH_RESULT::NOT_CONFIRMED:
                        return ['form' => $form, 'errors' => $errors = ['name' => 'not_confirmed']];
                    case AUTH_RESULT::SUCCESS:
                        $account = $this->getAccountTable()->getAccountBy(['name' => $account->getName()]);
                        SessionService::createUserSession($account);

                        return $this->redirect()->toRoute('account', [
                            'action' => 'profile',
                        ]);
                }
            } else {
                $errors = $form->getMessages();

                return ['form' => $form, 'errors' => $errors];
            }
        }

        return ['form' => $form];
    }

    public function logoutAction()
    {
        SessionService::destroyUserSession();

        return $this->redirect()->toRoute('auth', [
            'action' => 'logoutsuccess',
        ]);
    }

    public function registersuccessAction()
    {
    }

    public function logoutsuccessAction()
    {
    }

    /**
     * @param Account $account
     *
     * @return int
     */
    private function authenticate($account)
    {
        $dbAcc = $this->getAccountTable()->getAccountBy(['name' => $account->getName()]);
        if (!$dbAcc) {
            return AUTH_RESULT::NOT_FOUND;
        }
        $hashedPw = hash('sha256', $account->getPassword()) . Constants::SALT;
        if ($hashedPw != $dbAcc->getPassword()) {
            return AUTH_RESULT::WRONG_CREDENTIALS;
        }
        if ($dbAcc->getRole() == Role::NOT_ACTIVATED) {
            return AUTH_RESULT::NOT_CONFIRMED;
        }

        return AUTH_RESULT::SUCCESS;
    }

    /**
     * Sends out a confirmation mail to the registered account
     *
     * @param Account $account
     */
    private function sendConfirmationMail(Account $account)
    {
        $mailText = "Congratulations " . $account->getName() . ", you registered at Eternal Deztiny. To complete your registration, ";
        $mailText .= "follow the link:\n" . $_SERVER['SERVER_NAME'] . "/account/activate/" . $account->getUserHash();

        $this->appMailService->sendMail($account->getEmail(), 'Your registration at Eternal Deztiny', $mailText);
    }

}
