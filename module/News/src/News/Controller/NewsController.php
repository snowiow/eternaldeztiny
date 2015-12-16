<?php

namespace News\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Account\Model\Role;
use Account\Service\PermissionChecker;

use News\Form\NewsForm;
use News\Form\CommentForm;
use News\Model\News;
use News\Model\Comment;
use News\Model\NewsCategoryTable;
use News\Model\NewsTable;
use News\Model\CommentTable;

class NewsController extends AbstractActionController
{
    /**
     * @var NewsTable
     */
    private $newsTable;

    /**
     * @var NewsCategoryTable
     */
    private $newsCategoryTable;

    /**
     * @var CommentTable
     */
    private $commentTable;

    public function indexAction()
    {
        $name    = '';
        $session = new \Zend\Session\Container('user');
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

        $form->get('category_id')->setValueOptions($this->createCategorySelect());

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
            return $this->redirect()->toRoute('news');
        }
        $session = new \Zend\Session\Container('user');
        if (!isset($session->id) || $session->id != $news->getAccountId()) {
            return $this->redirect()->toRoute('account',
                [
                    'action' => 'noright',
                ]
            );
        }

        $form = new NewsForm();
        $form->get('category_id')->setValueOptions($this->createCategorySelect());
        $form->bind($news);
        $form->get('category_id')->setValue($news->getCategoryId());
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

    public function detailAction()
    {
        $name       = '';
        $account_id = 0;
        $session    = $session    = new \Zend\Session\Container('user');
        if (isset($session) && !empty($session->name)) {
            $name       = $session->name;
            $account_id = (int) $session->id;
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('news');
        }

        $comments = $this->getCommentTable()->getCommentsByNewsId($id);
        $form     = new CommentForm();
        $form->get('news_id')->setValue($id);
        $form->get('account_id')->setValue($account_id);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $comment = new Comment();
            $form->setInputFilter($comment->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $comment->exchangeArray($form->getData());
                $this->getCommentTable()->saveComment($comment);
                return $this->redirect()->toRoute('news', [
                    'action' => 'detail',
                    'id'     => $id,
                ]);
            }
            return [
                'errors'      => $form->getMessages(),
                'accountName' => $name,
                'news'        => $this->getNewsTable()->getNews($id),
                'comments'    => $comments,
                'form'        => $form,
            ];
        }
        return [
            'accountName' => $name,
            'news'        => $this->getNewsTable()->getNews($id),
            'comments'    => $comments,
            'form'        => $form,
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

    public function getNewsCategoryTable()
    {
        if (!$this->newsCategoryTable) {
            $sm                      = $this->getServiceLocator();
            $this->newsCategoryTable = $sm->get('News\Model\NewsCategoryTable');
        }
        return $this->newsCategoryTable;
    }

    /**
     * @return CommentTable
     */
    public function getCommentTable()
    {
        if (!$this->commentTable) {
            $sm                 = $this->getServiceLocator();
            $this->commentTable = $sm->get('News\Model\CommentTable');
        }
        return $this->commentTable;
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

    /**
     * Creates an associative array of the id and names of all created categories.
     * @return array
     */
    private function createCategorySelect()
    {
        $categories = $this->getNewsCategoryTable()->fetchAll();

        foreach ($categories as $category) {
            $selectElems[$category->getId()] = $category->getName();
        }
        return $selectElems;
    }
}
