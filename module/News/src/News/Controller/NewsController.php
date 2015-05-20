<?php

namespace News\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator;

use News\Model\News;
use News\Model\NewsTable;
use News\Form\NewsForm;

class NewsController extends AbstractActionController
{
    /**
     * @var NewsTable
     */
    protected $newsTable;

    public function indexAction()
    {
        $name = '';
        $session = $session = new \Zend\Session\Container('user');
        if (isset($session) && !empty($session->name)) {
            $name = $session->name;
        }

        $paginator = $this->getNewsTable()->fetchAll(true);
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(5);
        return new ViewModel([
            'accountName' => $name,
            'paginator' => $paginator,
        ]);
    }

    /**
     * Adds a new News to the db or opens up the form for adding, if it isn't opened yet.
     *
     * @return array|\Zend\Http\Response
     * @throws \Exception
     */
    public function addAction()
    {
        $form = new NewsForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $news = new News();
            $form->setInputFilter($news->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $news->exchangeArray($form->getData());
                $this->getNewsTable()->saveNews($news);

                return $this->redirect()->toRoute('news');
            }
        }
        $session = $session = new \Zend\Session\Container('user');
        if (!isset($session->role) || $session->role < 4) {
            return $this->redirect()->toRoute('account',
                [
                    'action' => 'noright',
                ]
            );
        }

        return ['form' => $form, 'accountId' => $session->id];
    }

    /**
     * Edits a already existing news and updates it-
     *
     * @return array|\Zend\Http\Response
     * @throws \Exception
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('news', [
                'action' => 'add'
            ]);
        }

        try {
            $news = $this->getNewsTable()->getNews($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('news', [
                'action' => 'index'
            ]);
        }

        $form = new NewsForm();
        $form->bind($news);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($news->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getNewsTable()->saveNews($news);

                return $this->redirect()->toRoute('news');
            }
        }
        $session = $session = new \Zend\Session\Container('user');
        if (!isset($session->id) || $session->id != $news->getAccountId()) {
            return $this->redirect()->toRoute('account',
                [
                    'action' => 'noright',
                ]
            );
        }

        return [
            'id'        => $id,
            'form'      => $form,
            'accountId' => $session->id,
        ];
    }

    /**
     * Deletes the NewsPost which is chosen.
     *
     * @return array|\Zend\Http\Response
     * @throws \Exception
     */
    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('news');
        }

        try {
            $news = $this->getNewsTable()->getNews($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('news', [
                'action' => 'index'
            ]);
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');
                $this->getNewsTable()->deleteNews($id);
            }

            return $this->redirect()->toRoute('news');
        }

        $session = $session = new \Zend\Session\Container('user');
        if (!isset($session->id) || $session->id != $news->getAccountId()) {
            return $this->redirect()->toRoute('account',
                [
                    'action' => 'noright',
                ]
            );
        }

        return [
            'id'   => $id,
            'news' => $this->getNewsTable()->getNews($id),
        ];
    }

    /**
     * @return array|NewsTable|object
     */
    public function getNewsTable()
    {
        if (!$this->newsTable) {
            $sm = $this->getServiceLocator();
            $this->newsTable = $sm->get('News\Model\NewsTable');
        }

        return $this->newsTable;
    }
}
