<?php

namespace News\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator;

use Account\Model\Role;
use Account\Service\PermissionChecker;

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
        $name    = '';
        $session = $session = new \Zend\Session\Container('user');
        if (isset($session) && !empty($session->name)) {
            $name = $session->name;
        }

        $paginator = $this->getNewsTable()->fetchAll(true);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(5);
        return new ViewModel([
            'accountName' => $name,
            'paginator'   => $paginator,
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
        $session = $session = new \Zend\Session\Container('user');
        if (!PermissionChecker::check(Role::ELDER)) {
            return $this->redirect()->toRoute('account',
                [
                    'action' => 'noright',
                ]
            );
        }

        $form = new NewsForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $news = new News();
            $form->setInputFilter($news->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $news->exchangeArray($form->getData());
                if ($this->checkWordLengths($news->getContent())) {
                    $this->getNewsTable()->saveNews($news);

                    return $this->redirect()->toRoute('news');
                } else {
                    return ['form' => $form, 'accountId' => $session->id, 'error' => 'tooLong'];
                }
            } else {
                return ['form' => $form, 'accountId' => $session->id, 'error' => 'tooLong'];
            }

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
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('news', [
                'action' => 'add',
            ]);
        }

        try {
            $news = $this->getNewsTable()->getNews($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('news', [
                'action' => 'index',
            ]);
        }
        $session = $session = new \Zend\Session\Container('user');
        if (!isset($session->id) || $session->id != $news->getAccountId()) {
            return $this->redirect()->toRoute('account',
                [
                    'action' => 'noright',
                ]
            );
        }

        $form = new NewsForm();
        $form->bind($news);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($news->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                if ($this->checkWordLengths($news->getContent())) {
                    $this->getNewsTable()->saveNews($news);

                    return $this->redirect()->toRoute('news');
                } else {
                    return [
                        'id'        => $id,
                        'form'      => $form,
                        'accountId' => $session->id,
                        'error'     => 'tooLong',
                    ];
                }
            } else {
                return [
                    'id'        => $id,
                    'form'      => $form,
                    'accountId' => $session->id,
                    'error'     => 'tooLong',
                ];
            }
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
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('news');
        }

        try {
            $news = $this->getNewsTable()->getNews($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('news');
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
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
            $sm              = $this->getServiceLocator();
            $this->newsTable = $sm->get('News\Model\NewsTable');
        }

        return $this->newsTable;
    }

    /**
     * Checks the word lengths of all words in a given text.
     * If any word is longer than 100 chars false is returned,
     * true otherwise
     * @param string $string the given text
     * @return bool true if no word is longer than 100 characters, false otherwise
     */
    private function checkWordLengths($string)
    {
        $words = explode(' ', $string);
        foreach ($words as $word) {
            if (strlen($word) > 100) {
                return false;
            }

        }
        return true;
    }
}
