<?php

namespace Warclaim\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Warclaim\Form\WarclaimForm;
use Warclaim\Model\Warclaim;

class WarclaimController extends AbstractActionController
{
    public $warclaimTable;

    public function createAction()
    {
        $form    = new WarclaimForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            if (array_key_exists('select', $request->getPost())) {
                return new ViewModel([
                    'form' => $form,
                    'size' => $request->getPost()['select'],
                ]);
            }
            $warclaim = new Warclaim();
            $form->setInputFilter($warclaim->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $warclaim->exchangeArray($form->getData());
                $this->getWarclaimTable()->saveWarclaim($warclaim);

                return $this->redirect()->toRoute('news');
            } else {
                $errors = $form->getMessages();

                return new ViewModel([
                    'form'   => $form,
                    'size'   => 10,
                    'errors' => $errors,
                ]);
            }
        }
        return new ViewModel(
            [
                'form' => $form,
                'size' => 10,
            ]
        );
    }

    /**
     * @return array|ApplicationTable|object
     */
    public function getWarclaimTable()
    {
        if (!$this->warclaimTable) {
            $sm                  = $this->getServiceLocator();
            $this->warclaimTable = $sm->get('ApplyNow\Model\ApplicationTable');
        }

        return $this->warclaimTable;
    }
}
