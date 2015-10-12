<?php

namespace Account\Controller;

use Zend\View\Model\ViewModel;
use Zend\Session\Container;

use Account\Model\Role;
use Account\Model\Account;
use Account\Form\SearchUserForm;
use Account\Service\PermissionChecker;

class AdminController extends AbstractAccountController
{
    /**
     * ApplicationTable
     */
    private $applicationTable;

    /**
     * @var MediaTable
     */
    private $mediaTable;

    /**
     * @var NewsTable
     */
    private $newsTable;

    public function setRolesAction()
    {
        if (!PermissionChecker::check(Role::CO)) {
            return $this->redirect()->toRoute('account', ['action' => 'noright']);
        }

        $form = new SearchUserForm();

        $request = $this->getRequest();

        $paginator = $this->getAccountTable()->getUsersAndAbove(true);
        $page      = (int) $this->params()->fromQuery('page', 1);
        $name      = $this->params()->fromQuery('name', '');
        $role      = $this->params()->fromQuery('role', '');
        $account   = new Account();
        $form->setInputFilter($account->getUserSearchInputFilter());
        $form->setData(['name' => $name, 'role' => $role]);

        if ($form->isValid()) {
            $account->exchangeArray($form->getData());
            $paginator = $this->getAccountTable()->getUsersAndAbove(true, $name, $role);
        }
        $role_strings = Role::getAllRoles();
        $paginator->setCurrentPageNumber($page);
        $paginator->setItemCountPerPage(25);
        return [
            'form'         => $form,
            'users'        => $paginator,
            'role_strings' => $role_strings,
        ];
    }

    public function updateroleAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $data    = $request->getPost();
            $account = $this->getAccountTable()->getAccountBy(['id' => $data['id']]);
            $account->setRole($data['select']);
            $this->getAccountTable()->saveAccount($account);
        }
        $this->redirect()->toRoute('admin', ['action' => 'setroles']);
    }

    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('news');
        }
        if (!PermissionChecker::check(Role::CO)) {
            return $this->redirect()->toRoute('account', ['action' => 'noright']);
        }

        try {
            $account = $this->getAccountTable()->getAccount($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('news');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                //delete all related news
                $news = $this->getNewsTable()->getNewsByAccoundId($id);
                foreach ($news as $n) {
                    $this->getNewsTable()->deleteNews($n->getId());
                }
                //delete all related media
                $media = $this->getMediaTable()->getMediaByAccoundId($id);
                foreach ($media as $m) {
                    $this->getMediaTable()->deleteMedia($m->getId());
                }
                //reset all applications to the account who is executing this actio
                $applications = $this->getApplicationTable()->getApplicationsByAccountId($id);
                $session      = new Container('user');
                foreach ($applications as $application) {
                    $application->setProcessedBy($session->id);
                    $this->getApplicationTable()->saveApplication($application);
                }
                //TODO: If new relations have to be deleted, replace these into their own function

                $this->getAccountTable()->deleteAccount($id);
            }
            return $this->redirect()->toRoute('admin', ['action' => 'setroles']);
        }

        return ['account' => $account];
    }

    /**
     * Retrieve the newsTable
     *
     * @return NewsTable
     */
    public function getNewsTable()
    {
        if (!$this->newsTable) {
            $sm              = $this->getServiceLocator();
            $this->newsTable = $sm->get('News\Model\NewsTable');
        }

        return $this->newsTable;
    }

    /**
     * Retrieve the mediaTable
     *
     * @return MediaTable
     */
    public function getMediaTable()
    {
        if (!$this->mediaTable) {
            $sm               = $this->getServiceLocator();
            $this->mediaTable = $sm->get('Media\Model\MediaTable');
        }

        return $this->mediaTable;
    }

    public function getApplicationTable()
    {
        if (!$this->applicationTable) {
            $sm                     = $this->getServiceLocator();
            $this->applicationTable = $sm->get('ApplyNow\Model\ApplicationTable');
        }

        return $this->applicationTable;
    }
}
