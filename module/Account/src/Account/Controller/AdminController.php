<?php

namespace Account\Controller;

use Zend\View\Model\ViewModel;

use Account\Model\Role;
use Account\Model\Account;
use Account\Form\SearchUserForm;
use Account\Service\PermissionChecker;

class AdminController extends AbstractAccountController
{
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
                $id   = (int) $request->getPost('id');
                $news = $this->getNewsTable()->getNewsByAccoundId($id);
                foreach ($news as $n) {
                    $this->getNewsTable()->deleteNews($n->getId());
                }

                $media = $this->getMediaTable()->getMediaByAccoundId($id);
                foreach ($media as $m) {
                    $this->getMediaTable()->deleteMedia($m->getId());
                }
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

}
