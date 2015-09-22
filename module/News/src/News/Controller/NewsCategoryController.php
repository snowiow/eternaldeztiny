<?php

namespace News\Controller;

use Account\Model\Role;
use Account\Service\PermissionChecker;
use News\Form\NewsCategoryForm;
use News\Model\NewsCategory;
use News\Model\NewsCategoryTable;
use Zend\File\Transfer\Adapter\Http;
use Zend\Http\PhpEnvironment\Request;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\File\Size;

class NewsCategoryController extends AbstractActionController
{
    private $newsCategoryTable;

    public function editAction()
    {
        if (!PermissionChecker::check(Role::CO)) {
            return $this->redirect()->toRoute('account',
                [
                    'action' => 'noright',
                ]
            );
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('newscategory', ['action' => 'add']);
        }
        try {
            $nc = $this->getNewsCategoryTable()->getNewsCategoryBy(['id' => $id]);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('news');
        }

        $form = new NewsCategoryForm();
        $form->bind($nc);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            return $this->handleForm($request, $form, $nc, $id);
        }
        return ['form' => $form, 'id' => $id];
    }

    public function deleteAction()
    {
        if (!PermissionChecker::check(Role::CO)) {
            return $this->redirect()->toRoute('account',
                [
                    'action' => 'noright',
                ]
            );
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('newscategory');
        }

        try {
            $nc = $this->getNewsCategoryTable()->getNewsCategoryBy(['id' => $id]);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('newscategory');
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del === 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getNewsCategoryTable()->deleteNewsCategory($id);
            }
            return $this->redirect()->toRoute('newscategory');
        }
        return ['id' => $id, 'nc' => $nc];
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
        $form = new NewsCategoryForm();
        $form->get('name')->setAttribute('required', 'required');
        $form->get('path')->setAttribute('required', 'required');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $nc = new NewsCategory();
            $form->setInputFilter($nc->getInputFilter());
            return $this->handleForm($request, $form, $nc);
        }
        return ['form' => $form];
    }

    public function indexAction()
    {
        if (!PermissionChecker::check(Role::CO)) {
            return $this->redirect()->toRoute('account',
                [
                    'action' => 'noright',
                ]
            );
        }

        $categories = $this->getNewsCategoryTable()->fetchAll();

        return [
            'categories' => $categories,
        ];
    }

    /**
     * @return NewsCategoryTable
     */
    private function getNewsCategoryTable()
    {
        if (!$this->newsCategoryTable) {
            $sm                      = $this->getServiceLocator();
            $this->newsCategoryTable = $sm->get('News\Model\NewsCategoryTable');
        }
        return $this->newsCategoryTable;
    }

    /**
     * Handles a given form for the add and edit action
     * @return array Array for the view, containing the form and maybe id and errors
     */
    private function handleForm(Request &$request, NewsCategoryForm &$form, NewsCategory &$nc, $id = 0)
    {
        $form->setInputFilter($nc->getInputFilter());
        $post = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );
        $form->setData($post);
        if ($form->isValid()) {
            $nc->exchangeArray($post);
            $old = $this->getNewsCategoryTable()->getNewsCategoryBy(['id' => $id]);
            if ($this->getNewsCategoryTable()->getNewsCategoryBy(['name' => $nc->getName()]) &&
                ($id === 0 || $nc->getName() !== $old->getName())) {
                $errors['name'] = ['exists' => 'A category with this name already exists'];
                $form->get('name')->setMessages($errors);
                if (!$id) {
                    return ['form' => $form, 'errors' => $errors];
                }

                return ['form' => $form, 'errors' => $errors, 'id' => $id];
            }
            $size = new Size([
                'min' => 20,
                'max' => 20000,
            ]);

            $adapter = new Http();
            $adapter->setValidators([$size], $post['path']);

            //Only throw error if a new category is created. New Categories need an image
            if (!$adapter->isValid() && $id === 0) {
                $errors = $adapter->getMessages();

                return ['form' => $form, 'errors' => $errors];
            }

            $dir = getcwd() . '/public/news_cat/';
            //A file was given, so it will be saved on the server
            if ($adapter->isValid()) {
                if (!file_exists($dir)) {
                    mkdir($dir);
                }
                $pic  = $post['path'];
                $file = file_get_contents($pic['tmp_name']);
                file_put_contents($dir . $nc->getName() . '.png', $file);
                //wait a bit to write the file. The redirect is faster otherwise
                sleep(2);
            } else {
                //No new file was given, so update the filename to the new name
                rename($dir . $old->getName() . '.png', $dir . $nc->getName() . '.png');
            }
            $this->getNewsCategoryTable()->saveNewsCategory($nc);
            return $this->redirect()->toRoute('newscategory');
        }
        $errors = $form->getMessages();
        if ($id) {
            return ['form' => $form, 'errors' => $errors, 'id' => $id];
        }
        return ['form' => $form, 'errors' => $errors];
    }
}
