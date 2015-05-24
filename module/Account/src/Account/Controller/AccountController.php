<?php

namespace Account\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\FileInput;

use Account\Form\RegisterForm;
use Account\Form\LoginForm;
use Account\Form\UploadAvatarForm;
use Account\Model\Account;
use Account\Model\Role;
use Account\Model\AccountTable;

use AppMail\Service\AppMailServiceInterface;
use Application\Constants;

interface AUTH_RESULT
{
    const SUCCESS           = 0;
    const WRONG_CREDENTIALS = 1;
    const NOT_FOUND         = 1 << 1;
    const NOT_CONFIRMED     = 1 << 2;
}

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

    public function profileAction()
    {
    }

    public function uploadAvatarAction()
    {
        $form = new UploadavatarForm();
        $form->get('submit')->setValue('Change Avatar');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = array_merge_recursive($request->getPost()->toArray(),
                $request->getFiles()->toArray());

            $form->setData($post);
            if ($form->isValid()) {
                $data = $form->getData();
                if (strstr($data['file']['type'], 'image')) {
                    $session = $session = new \Zend\Session\Container('user');
                    $file = file_get_contents($data['file']['tmp_name']);
                    file_put_contents(getcwd() . '/public/users/' . $session->name . '/avatar.jpg',
                        $file);
                    $account = $this->getAccountTable()->getAccount($session->id);
                    $filePath = '/users/' . $session->name . '/avatar.jpg';
                    $account->setAvatar($filePath);
                    $this->getAccountTable()->saveAccount($account);
                    $session->avatar = $filePath;
                    sleep(0.5);
                    return $this->redirect()->toRoute('account', [
                            'action' => 'profile',
                    ]);
                }
                else {
                    return ['form' => $form, 'errors' => $errors = ['file' => 'not_valid']];
                }
            }
        }
        return ['form' => $form];
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
                        $this->getAccountTable()->saveAccount($account);
                        $this->sendConfirmationMail($account);
                    } else {
                        return ['form' => $form, 'errors' => $errors = ['email' => 'email_taken']];
                    }
                } else {
                    return ['form' => $form, 'errors' => $errors = ['name' => 'name_taken']];
                }

                return $this->redirect()->toRoute('account', [
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
                        $this->createUserSession($account);

                        return $this->redirect()->toRoute('account', [
                            'action' => 'loginsuccess',
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
        $this->destroyUserSession();

        return $this->redirect()->toRoute('account', [
            'action' => 'logoutsuccess',
        ]);
    }

    public function registersuccessAction()
    {
    }

    public function loginsuccessAction()
    {
    }

    public function logoutsuccessAction()
    {
    }

    public function norightAction()
    {
    }

    /**
     * Activates an account which isn't activated yet.
     * Deletes it's userhash, because it's not needed anymore.
     * Creates a folder for stuff of the user.
     */
    public function activateAction()
    {
        $userhash = $this->params()->fromRoute('id', 0);
        $account = $this->getAccountTable()->getAccountBy(['userhash' => $userhash]);
        if ($account->getRole() == Role::NOT_ACTIVATED) {
            $account->setRole(Role::USER);
            $account->setUserHash('');
            $this->accountTable->saveAccount($account);
            mkdir(getcwd() . '/public/users/' . $account->getName());
        }
    }

    /**
     * Retrieve the accountTable
     *
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
        $mailText = $mailText . "follow the link:\ned.com/account/activate/" . $account->getUserHash();

        $this->appMailService->sendMail($account->getEmail(), 'Your registration at Eternal Deztiny', $mailText);
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
     * Creates a user session in the user namespace.
     * id, role, name, avatar and registered are accessable afterwards.
     * @param \Account\Model\Account $account
     */
    private function createUserSession(Account $account)
    {
        $session = new Container('user');
        $session->id = $account->getId();
        $session->role = $account->getRole();
        $session->name = $account->getName();
        $session->avatar = $account->getAvatar();
        $session->registered = $account->getDateRegistered();
    }

    /**
     * Destroys the user session in the user namespace.
     */
    private function destroyUserSession()
    {
        $session = new Container('user');
        if (isset($session->id)) {
            unset($session->id);
        }

        if (isset($session->role)) {
            unset($session->role);
        }

        if (isset($session->name)) {
            unset($session->name);
        }

        if (isset($session->avatar)) {
            unset($session->avatar);
        }

        if (isset($session->registered)) {
            unset($session->registered);
        }
    }
}
