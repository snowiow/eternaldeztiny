<?php

namespace Warlog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Account\Model\Role;
use Account\Service\PermissionChecker;
use Application\Constants;
use Warlog\Form\WarlogForm;
use Warlog\Model\Warlog;
use Warlog\Model\WarlogTable;

class WarlogController extends AbstractActionController
{
    /**
     * @var WarlogTable
     */
    private $warlogTable;

    public function indexAction()
    {
        $warlog = $this->getWarlogTable()->getWarlog();
        if (!$warlog) {
            return new ViewModel([
                'warlog'          => new Warlog(),
                'win_percentage'  => 0.1,
                'loss_percentage' => 0.1,
                'draw_percentage' => 0.1,
            ]);
        }
        $total           = $warlog->getWins() + $warlog->getLosses() + $warlog->getDraws();
        $win_percentage  = $warlog->getWins() > 0 ? $warlog->getWins() / $total * 100 : 0.1;
        $loss_percentage = $warlog->getLosses() > 0 ? $warlog->getLosses() / $total * 100 : 0.1;
        $draw_percentage = 100 - $win_percentage - $loss_percentage;
        return new ViewModel([
            'warlog'          => $warlog,
            'win_percentage'  => $win_percentage,
            'loss_percentage' => $loss_percentage,
            'draw_percentage' => $draw_percentage,
        ]);
    }

    public function uploadAction()
    {
        if (!PermissionChecker::check(Role::CO)) {
            return $this->redirect()->toRoute('account', ['action' => 'noright']);
        }

        $form = new WarlogForm();
        $form->get('submit')->setValue('Upload Warlog');

        $warlog = $this->getWarlogTable()->getWarlog();
        if (!$warlog) {
            $warlog = new Warlog();
        }

        $form->bind($warlog);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($warlog->getInputFilter());
            $post = array_merge_recursive($request->getPost()->toArray(),
                $request->getFiles()->toArray());

            $form->setData($post);
            if ($form->isValid()) {
                $data = $form->getData();
                if (strstr($data->getFile()['type'], 'image')) {
                    $file = file_get_contents($data->getFile()['tmp_name']);
                    file_put_contents(getcwd() . '/public/img/warlog/warlog.png', $file);
                    sleep(0.5);
                }

                $this->getWarlogTable()->saveWarlog($warlog);
                return $this->redirect()->toRoute('warlog');
            } else {
                $errors = $form->getMessages();

                return ['form' => $form, 'errors' => $errors];
            }
        }
        return ['form' => $form];
    }

    public function winAction()
    {
        if (!PermissionChecker::check(Role::CO)) {
            return $this->redirect()->toRoute('account',
                [
                    'action' => 'noright',
                ]
            );
        }
        $warlog = $this->getWarlogTable()->getWarlog();
        if (!$warlog) {
            return $this->redirect()->toRoute('account', ['action' => 'profile']);
        }
        $warlog->setWins($warlog->getWins() + 1);
        $this->getWarlogTable()->saveWarlog($warlog);

        return $this->redirect()->toRoute('warlog');
    }

    public function lossAction()
    {
        if (!PermissionChecker::check(Role::CO)) {
            return $this->redirect()->toRoute('account',
                [
                    'action' => 'noright',
                ]
            );
        }

        $warlog = $this->getWarlogTable()->getWarlog();
        if (!$warlog) {
            return $this->redirect()->toRoute('account', ['action' => 'profile']);
        }
        $warlog->setLosses($warlog->getLosses() + 1);
        $this->getWarlogTable()->saveWarlog($warlog);

        return $this->redirect()->toRoute('warlog');
    }

    public function drawAction()
    {
        if (!PermissionChecker::check(Role::CO)) {
            return $this->redirect()->toRoute('account',
                [
                    'action' => 'noright',
                ]
            );
        }

        $warlog = $this->getWarlogTable()->getWarlog();
        if (!$warlog) {
            return $this->redirect()->toRoute('account', ['action' => 'profile']);
        }

        $warlog->setDraws($warlog->getDraws() + 1);
        $this->getWarlogTable()->saveWarlog($warlog);

        return $this->redirect()->toRoute('warlog');
    }

    /**
     * @return array|WarlogTable|object
     */
    public function getWarlogTable()
    {
        if (!$this->warlogTable) {
            $sm                = $this->getServiceLocator();
            $this->warlogTable = $sm->get('Warlog\Model\WarlogTable');
        }

        return $this->warlogTable;
    }
}
