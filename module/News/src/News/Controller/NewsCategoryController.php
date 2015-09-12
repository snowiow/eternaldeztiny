<?php

namespace News\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\File\Transfer\Adapter\Http;
use Zend\Validator\File\Size;

use Account\Model\Role;
use Account\Service\PermissionChecker;

use News\Form\NewsCategoryForm;
use News\Model\NewsCategoryTable;
use News\Model\NewsCategory;

class NewsCategoryController extends AbstractActionController
{
    private $newsCategoryTable;

    public function editAction()
    {

    }

    public function addAction()
    {
        if (!PermissionChecker::check(Role::CO)) {
            return $this->redirect()->toRoute('account',
                [
                    'action' => 'noright',
                ]
            );
        }
        $form    = new NewsCategoryForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $nc = new NewsCategory();
            $form->setInputFilter($nc->getInputFilter());
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $form->setData($post);
            if ($form->isValid()) {
                $nc->exchangeArray($post);
                if ($this->getNewsCategoryTable()->getNewsCategoryBy(['name' => $nc->getName()])) {
                    $errors['name'] = ['exists' => 'A category with this name already exists'];
                    $form->get('name')->setMessages($errors);
                    return ['form' => $form, 'errors' => $errors];
                }
                $size = new Size([
                    'min' => 20,
                    'max' => 20000,
                ]);

                $adapter = new Http();
                $adapter->setValidators([$size], $post['path']);
                if (!$adapter->isValid()) {
                    $errors = $adapter->getMessages();
                    return ['form' => $form, 'errors' => $errors];
                }
                $path = getcwd() . '/public/news/' . $nc->getName() . '.png';
                $pic  = $post['path'];
                $file = file_get_contents($pic['tmp_name']);
                file_put_contents($path, $file);
                $this->getNewsCategoryTable()->saveNewsCategory($nc);
                $this->redirect()->toRoute('news');
            } else {
                $errors = $form->getMessages();
                return ['form' => $form, 'errors' => $errors];
            }
        }
        return ['form' => $form];
    }

    public function indexAction()
    {

    }

    /**
     * @return NewsCategoryTable
     */
    public function getNewsCategoryTable()
    {
        if (!$this->newsCategoryTable) {
            $sm                      = $this->getServiceLocator();
            $this->newsCategoryTable = $sm->get('News\Model\NewsCategoryTable');
        }
        return $this->newsCategoryTable;
    }
}
