<?php

namespace Media\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator;

use Media\Model\Media;
use Media\Model\MediaTable;
use Media\Form\MediaForm;

class MediaController extends AbstractActionController
{
    /**
     * @var NewsTable
     */
    protected $mediaTable;

    public function indexAction()
    {
        $name    = '';
        $session = $session = new \Zend\Session\Container('user');
        if (isset($session) && !empty($session->name)) {
            $name = $session->name;
        }

        $paginator = $this->getMediaTable()->fetchAll(true);
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
        if (!isset($session->role) || $session->role < \Account\Model\Role::MEMBER) {
            return $this->redirect()->toRoute('account',
                [
                    'action' => 'noright',
                ]
            );
        }

        $form = new MediaForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $media = new Media();
            $form->setInputFilter($media->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $media->exchangeArray($form->getData());
                $this->getMediaTable()->saveMedia($media);

                return $this->redirect()->toRoute('media');
            } else {
                $errors = $form->getMessages();
                return ['form' => $form, 'accountId' => $session->id, 'errors' => $errors];
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
            return $this->redirect()->toRoute('media', [
                'action' => 'add',
            ]);
        }

        try {
            $media = $this->getMediaTable()->getMedia($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('media', [
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

        $form = new MediaForm();
        $form->bind($media);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($media->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getMediaTable()->saveMedia($media);

                return $this->redirect()->toRoute('news');
            } else {
                $errors = $form->getMessages();
                return [
                    'id'        => $id,
                    'form'      => $form,
                    'accountId' => $session->id,
                    'errors'    => $errors,
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
            return $this->redirect()->toRoute('media');
        }

        try {
            $media = $this->getMediaTable()->getMedia($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('media', [
                'action' => 'index',
            ]);
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getMediaTable()->deleteMedia($id);
            }

            return $this->redirect()->toRoute('media');
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
            'id'    => $id,
            'media' => $this->getMediaTable()->getMedia($id),
        ];
    }

    /**
     * @return array|NewsTable|object
     */
    public function getMediaTable()
    {
        if (!$this->mediaTable) {
            $sm               = $this->getServiceLocator();
            $this->mediaTable = $sm->get('Media\Model\MediaTable');
        }

        return $this->mediaTable;
    }

    private function embedVideo($url)
    {
        if (substr($url, 0, 7) === 'youtube') {
            $url = 'https://www.' . $url;
        }

        if (substr($url, 0, 4) === 'www.') {
            $url = 'https://' . $url;
        }

        $url = substr_replace($url, '', 23, 8);

    }
}
