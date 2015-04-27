<?php

namespace News\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class NewsController extends AbstractActionController 
{
    protected $newsTable;

    public function indexAction() 
    {
        return new ViewModel(array(
            'news' => $this->getNewsTable()->fetchAll(),
        ));
    }

    public function addAction()
    {
    }

    public function editAction()
    {
    }

    public function deleteAction() 
    {
    }

    public function getNewsTable() 
    {
        if (!$this->newsTable) {
            $sm = $this->getServiceLocator();
            $this->newsTable = $sm->get('News\Model\NewsTable');
        }
        return $this->newsTable;
    }
}
