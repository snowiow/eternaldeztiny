<?php

namespace Warclaim\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Warclaim\Form\CreateForm;
use Warclaim\Form\CurrentForm;
use Warclaim\Form\PrecautionsForm;
use Warclaim\Model\Warclaim;
use Warclaim\Model\WarclaimTable;

use AppMail\Service\AppMailServiceInterface;
use Account\Model\Account;
use Account\Model\Role;
use Account\Service\PermissionChecker;
use Application\Constants;

class WarclaimController extends AbstractActionController
{
    /**
     * @var WarclaimTable
     */
    private $warclaimTable;

    /**
     * @var AccountTable
     */
    private $accountTable;

    /**
     * @var AppMailServiceInterface
     */
    private $appMailService;

    /**
     * @param AppMailServiceInterface $appMailService
     */
    public function __construct(AppMailServiceInterface $appMailService)
    {
        $this->appMailService = $appMailService;
    }

    public function createAction()
    {
        if (!PermissionChecker::check(Role::CO)) {
            return $this->redirect()->toRoute('account', ['action' => 'noright']);
        }

        if ($this->getWarclaimTable()->getCurrentWar()) {
            return $this->redirect()->toRoute('warclaim');
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
                $accounts = $this->getAccountTable()->getAccountsFromNames($warclaim->getAssignments());
                //Normalize targets to 1-.. from 0-..
                $assignments = [];
                foreach ($warclaim->getAssignments() as $k => $v) {
                    $assignments[$k + 1] = $v;
                }
                foreach ($accounts as $account) {
                    $this->sendAssignmentMail($account, $assignments);
                }

                $this->getWarclaimTable()->saveWarclaim($warclaim);
                return $this->redirect()->toRoute('warclaim');
            } else {
                $errors = $form->getMessages();
                return [
                    'form'     => $form,
                    'size'     => $size,
                    'errors'   => $errors,
                    'members'  => $members,
                    'opponent' => $opponent,
                ];
            }
        }
        return [
            'form'     => $form,
            'size'     => $size,
            'members'  => $members,
            'opponent' => $opponent,
        ];
    }

    public function closeAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('news');
        }
        try {
            $warclaim = $this->getWarclaimTable()->getWarclaim($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('news');
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $close = $request->getPost('close', 'Abort');

            if ($close !== 'Abort') {
                $id       = (int) $request->getPost('id');
                $warclaim = $this->getWarclaimTable()->getWarclaim($id);
                $warclaim->setOpen(false);
                $this->getWarclaimTable()->saveWarclaim($warclaim);
                switch ($close) {
                case 'Close':
                    return $this->redirect()->toRoute('account', ['action' => 'profile']);
                case 'Win':
                    return $this->redirect()->toRoute('warlog', ['action' => 'win']);
                case 'Loss':
                    return $this->redirect()->toRoute('warlog', ['action' => 'loss']);
                case 'Draw':
                    return $this->redirect()->toRoute('warlog', ['action' => 'draw']);
                }
            }
            return $this->redirect()->toRoute('warclaim');
        }

        if (!PermissionChecker::check(Role::CO)) {
            return $this->redirect()->toRoute('account',
                [
                    'action' => 'noright',
                ]
            );
        }

        return [
            'warclaim' => $warclaim,
        ];
    }

    public function precautionsAction()
    {
        if (!PermissionChecker::check(Role::CO)) {
            return $this->redirect()->toRoute('account', ['action' => 'noright']);
        }

        if ($this->getWarclaimTable()->getCurrentWar()) {
            return $this->redirect()->toRoute('warclaim');
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
                    'id'       => 0,
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
        if (!$warclaim || !PermissionChecker::check(Role::MEMBER)) {
            return $this->redirect()->toRoute('warclaim', ['action' => 'nowar']);
        }

        $members = $this->getAccountTable()->getMembers();
        $session = new \Zend\Session\Container('user');
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
                    if (($warclaim->getCleanup()[$i] !== '' && $warclaim->getInfo()[$i] === '')) {
                        $errors[$i . 'i'] = ['no_info' => 'You have to give an info, when you attack.'];
                        $form->get($i . 'i')->setMessages($errors);
                    } elseif ($warclaim->getCleanup()[$i] === '' && $warclaim->getInfo()[$i] !== '') {
                        $errors[$i . 'c'] = ['no_info' => 'You have to give a cleanup, when you give an info.'];
                        $form->get($i . 'c')->setMessages($errors);
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

                return $this->redirect()->toRoute('warclaim');
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
     * @return array|WarclaimTable|object
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

    private function sendAssignmentMail(Account $account, array $assignments)
    {
        $target_main = implode(',', array_keys($assignments, $account->getName()));
        $target_mini = implode(',', array_keys($assignments, $account->getMini()));

        $text = 'Hello ' . $account->getName() . ". Your targets are:\n";

        if ($target_main) {
            $text .= $account->getName() . ': ' . $target_main . "\n";
        }
        if ($target_mini) {
            $text .= $account->getMini() . ': ' . $target_mini . "\n";
        }

        $text .= 'Good Luck. Further information can be found under ' .
        Constants::HOST . '/warclaim/';

        $this->appMailService->sendMail($account->getEmail(), 'A new war has started!', $text);
    }
}
