<?php

namespace Warlog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Warlog\Form\WarlogForm;
use Warlog\Model\Warlog;
use Warlog\Model\WarlogTable;
use Account\Model\Role;

use Application\Constants;

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
                'win_percentage'  => 0,
                'loss_percentage' => 0,
                'draw_percentage' => 0,
            ]);
        }
        $total = $warlog->getWins() + $warlog->getLosses() + $warlog->getDraws();
        return new ViewModel([
            'warlog'          => $warlog,
            'win_percentage'  => $warlog->getWins() / $total * 100,
            'loss_percentage' => $warlog->getLosses() / $total * 100,
            'draw_percentage' => $warlog->getDraws() / $total * 100,
        ]);
    }

    public function uploadAction()
    {
        $session = new \Zend\Session\Container('user');
        if ($session->role < Role::CO) {
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
