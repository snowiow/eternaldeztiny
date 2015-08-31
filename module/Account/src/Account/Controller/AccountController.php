<?php

namespace Account\Controller;

use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\FileInput;

use Account\Controller\AbstractAccountController;
use Account\Form\RegisterForm;
use Account\Form\LoginForm;
use Account\Form\EditProfileForm;
use Account\Form\LostPasswordForm;
use Account\Form\ResetPasswordForm;
use Account\Model\Account;
use Account\Model\Role;
use Account\Model\AccountTable;
use Account\Service\SessionService;

use AppMail\Service\AppMailServiceInterface;
use Application\Constants;
use ApplyNow\Model\ApplicationTable;
use Warclaim\Model\WarclaimTable;

class AccountController extends AbstractAccountController
{
    /**
     * @var WarclaimTable
     */
    private $warclaimTable;

    /**
     * @var ApplicationTable
     */
    private $applicationTable;

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
        $name              = $this->params()->fromRoute('id', '');
        $session           = new Container('user');
        $open_applications = $this->getApplicationTable()->getOpenApplications();
        $open_war          = $this->getWarclaimTable()->getCurrentWar();
        if ($session->name === $name || empty($name)) {
            return new ViewModel([
                'self'              => true,
                'account'           => $this->getAccountTable()->getAccountBy(['name' => $session->name]),
                'open_applications' => $open_applications->count(),
                'war'               => $open_war ? true : false,
            ]);
        }
        $account = $this->getAccountTable()->getAccountBy(['name' => $name]);

        if ($account) {
            return new ViewModel([
                'self'              => false,
                'account'           => $account,
                'open_applications' => $open_applications->count(),
                'war'               => $open_war ? true : false,
            ]);
        }
        return $this->redirect()->toRoute('account', ['action' => 'nouser']);
    }

    public function editAction()
    {
        $id      = (int) $this->params()->fromRoute('id', 0);
        $session = $session = new Container('user');
        if (!$id || $session->id != $id) {
            return $this->redirect()->toRoute('account', [
                'action' => 'noright',
            ]);
        }

        try {
            $account = $this->getAccountTable()->getAccount($session->id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('account', [
                'action' => 'noright',
            ]);
        }

        $form = new EditProfileForm();
        $form->bind($account);
        $form->get('submit')->setValue('Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            //Needs to be rebinded, because everything that won't get binded by the form will
            //be deleted
            $account = $this->getAccountTable()->getAccount($session->id);

            $post = array_merge_recursive($request->getPost()->toArray(),
                $request->getFiles()->toArray());
            $form->setData($post);
            if ($form->isValid()) {
                $data = $form->getData();
                $account->setMini($data->getMini('mini'));
                $file_arr = $request->getFiles()->toArray();
                if (strstr($file_arr['file']['type'], 'image')) {
                    $file = file_get_contents($file_arr['file']['tmp_name']);
                    file_put_contents(getcwd() . '/public/users/' . $session->name . '/avatar.jpg',
                        $file);
                    $filePath = '/users/' . $session->name . '/avatar.jpg';
                    $account->setAvatar($filePath);
                    $session->avatar = $filePath;
                    sleep(0.5);
                }
                $this->getAccountTable()->saveAccount($account);
                return $this->redirect()->toRoute('account', [
                    'action' => 'profile',
                ]);

            } else {
                $errors = $form->getMessages();
                return ['form' => $form, 'errors' => $errors, 'id' => $session->id];
            }
        }
        return ['form' => $form, 'id' => $session->id];
    }

    public function norightAction()
    {
    }

    public function nouserAction()
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
        $account  = $this->getAccountTable()->getAccountBy(['userhash' => $userhash]);
        if ($account && $account->getRole() == Role::NOT_ACTIVATED) {
            $account->setRole(Role::USER);
            $account->setUserHash(null);
            $this->accountTable->saveAccount($account);

            if (!file_exists(getcwd() . '/public/users/')) {
                mkdir(getcwd() . '/public/users/');
            }
            mkdir(getcwd() . '/public/users/' . $account->getName());
        }
    }

    public function lostpasswordAction()
    {
        $form    = new LostPasswordForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $account = new Account();
            $form->setInputFilter($account->getLostPasswordInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $account->exchangeArray($form->getData());
                $account = $this->getAccountTable()->getAccountBy(['email' => $account->getEmail()]);
                if (!$account) {
                    return $this->redirect()->toRoute('account', ['action' => 'nouser']);
                }
                $account->setUserHash(hash('sha256', $account->getName()));
                $this->getAccountTable()->saveAccount($account);
                $this->sendLostPasswordMail($account);

                return $this->redirect()->toRoute('account', ['action' => 'lostpasswordsuccess']);
            }
            return ['form' => $form, 'errors' => 'No valid E-Mail adress'];
        }
        return ['form' => $form];
    }

    public function resetpasswordAction()
    {
        $userhash = $this->params()->fromRoute('id', '');
        $account  = $this->getAccountTable()->getAccountBy(['userhash' => $userhash]);
        $form     = new ResetPasswordForm();
        $request  = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($account->getResetPasswordInputFilter());
            $form->setData($request->getPost());
            $userhash = $request->getPost()['userhash'];
            if ($form->isValid()) {
                if ($form->getData()['password'] !== $form->getData()['repeat']) {
                    $errors           = [];
                    $errors['repeat'] = ['not_same' => 'Passwords have to be the same'];
                    $form->get('repeat')->setMessages($errors);
                    return ['form' => $form, 'errors' => $errors, 'userhash' => $userhash];
                }

                $account  = $this->getAccountTable()->getAccountBy(['userhash' => $userhash]);
                $password = hash('sha256', $form->getData()['password']) . Constants::SALT;
                $account->setPassword($password);
                $account->setUserHash(null);
                $this->getAccountTable()->saveAccount($account);
                return $this->redirect()->toRoute('account', ['action' => 'resetpasswordsuccess']);
            }
            $errors = $form->getMessages();
            return ['form' => $form, 'errors' => $errors, 'userhash' => $userhash];
        }
        if (!$account || !$userhash) {
            return $this->redirect()->toRoute('account', ['action' => 'nouser']);
        }
        return ['form' => $form, 'userhash' => $userhash];
    }

    public function lostpasswordsuccessAction()
    {
    }

    public function resetpasswordsuccessAction()
    {
    }

    /**
     * @return array|ApplicationTable|object
     */
    public function getApplicationTable()
    {
        if (!$this->applicationTable) {
            $sm                     = $this->getServiceLocator();
            $this->applicationTable = $sm->get('ApplyNow\Model\ApplicationTable');
        }

        return $this->applicationTable;
    }

    public function getWarclaimTable()
    {
        if (!$this->warclaimTable) {
            $sm                  = $this->getServiceLocator();
            $this->warclaimTable = $sm->get('Warclaim\Model\WarclaimTable');
        }
        return $this->warclaimTable;
    }

    /**
     * Sends out a lost password mail to the given account
     * @param Account $account
     */
    private function sendLostPasswordMail(Account $account)
    {
        $mailText = 'Hello ' . $account->getName() . ', a password reset for your account was requested. ' .
        'If you didn\'t request a password reset, you don\'t need to do anything. If you really want to ' .
        'reset your password, please follow the given link: ' . $_SERVER['SERVER_NAME'] . '/account/resetpassword/' .
        $account->getUserHash();
        $this->appMailService->sendMail($account->getEmail(), 'Password Reset', $mailText);
    }
}
