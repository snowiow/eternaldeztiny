<?php

namespace ApplyNow\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use ApplyNow\Model\Application;
use ApplyNow\Form\ApplicationForm;

class ApplyNowController extends AbstractActionController
{
    protected $applicationTable;

    public function indexAction()
    {

    }

    public function applyAction()
    {
        $form = new ApplicationForm();
        $form->get('submit')->setValue('Apply Now!');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $application = new Application();
            $form->setInputFilter($application->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $application->exchangeArray($form->getData());
                $this->getApplicationTable()->saveApplication($application);

                return $this->redirect()->toRoute('news');
            }
        }
        return ['form' => $form];
    }
}
