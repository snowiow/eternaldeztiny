<?php

namespace ApplyNow\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\File\Size;
use Zend\View\Model\ViewModel;

use ApplyNow\Form\ApplicationForm;
use ApplyNow\Model\Application;

use AppMail\Service\AppMailServiceInterface;
use Account\Model\AccountTable;
use Account\Model\Role;
use Application\Constants;

class ApplyNowController extends AbstractActionController
{
    /**
     * @var ApplicationTable
     */
    protected $applicationTable;

    /**
     * @var AccountTable
     */
    protected $accountTable;

    /**
     * @var \AppMail\Service\AppMailServiceInterface
     */
    protected $appMailService;

    public function __construct(AppMailServiceInterface $appMailService)
    {
        $this->appMailService = $appMailService;
    }

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
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($post);

            if ($form->isValid()) {

                $data = array_merge_recursive(
                    $this->getRequest()->getPost()->toArray(),
                    $this->getRequest()->getFiles()->toArray()
                );
                $application->exchangeArray($data);
                $size = new Size([
                    'min' => 20,
                    'max' => 200000,
                ]);

                $adapter = new \Zend\File\Transfer\Adapter\Http();
                $adapter->setValidators([$size], $application->getProfilePic());
                $adapter->setValidators([$size], $application->getBasePic());
                if (!$adapter->isValid()) {
                    $errors = $adapter->getMessages();
                    return ['form' => $form, 'errors' => $errors];
                }
                $path   = '/applications/' . $application->getTag() . '/';
                $prefix = getcwd() . '/public';
                if (!file_exists($prefix . '/applications/')) {
                    mkdir($prefix . '/applications/');
                }
                if (!file_exists($prefix . $path)) {
                    mkdir($prefix . $path);
                }

                $basePic = $application->getBasePic();
                $ending  = pathinfo($basePic['name'], PATHINFO_EXTENSION);
                $application->setBasePic($path . 'basepic.' . $ending);
                $basePicFile = file_get_contents($basePic['tmp_name']);
                file_put_contents($prefix . $application->getBasePic(), $basePicFile);

                $profilePic = $application->getProfilePic();
                $ending     = pathinfo($profilePic['name'], PATHINFO_EXTENSION);
                $application->setProfilePic($path . 'profilepic.' . $ending);
                $profilePicFile = file_get_contents($profilePic['tmp_name']);
                file_put_contents($prefix . $application->getProfilePic(), $profilePicFile);

                try {
                    $this->getApplicationTable()->saveApplication($application);
                } catch (\Exception $e) {
                    return $this->redirect()->toRoute('applynow', ['action' => 'applyfailed']);
                }
                $id = $this->getApplicationTable()->getLastInsertedId();
                $application->setId($id);
                $accounts = $this->getAccountTable()->getLeadershipMails();

                foreach ($accounts as $account) {
                    $this->sendApplicationMail($application, $account->getEmail());
                }

                return $this->redirect()->toRoute('applynow', ['action' => 'applysuccess']);
            } else {
                $errors = $form->getMessages();
                return ['form' => $form, 'errors' => $errors];
            }
        }
        return ['form' => $form];
    }

    public function applysuccessAction()
    {
    }

    public function applyfailedAction()
    {
    }

    public function overviewAction()
    {
        $session = new \Zend\Session\Container('user');
        //TODO: Find a way to include Role
        if ($session->role < Role::ELDER) {
            return $this->redirect()->toRoute('account', ['action' => 'noright']);
        }

        $paginator = $this->getApplicationTable()->getProcessedApplications(true);
        $paginator->setCurrentPageNumber((int) $this->params()->fromQuery('page', 1));
        $paginator->setItemCountPerPage(20);

        return new ViewModel([
            'unprocessed' => $this->getApplicationTable()->getOpenApplications(),
            'processed'   => $paginator,
        ]);
    }

    public function detailAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        $session = new \Zend\Session\Container('user');

        if ($session->role < Role::ELDER) {
            return $this->redirect()->toRoute('account', ['action' => 'noright']);
        }
        $form = new ApplicationForm();
        $form->get('submit')->setValue('Process Application');

        $application = new Application();
        $form->setInputFilter($application->getInputFilter());

        $application = $this->getApplicationTable()->getApplication($id);
        $form->setData($application->getArrayCopy());

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost()->toArray();
            $application->setProcessed($data['processed']);
            $application->setProcessedBy((int) $session->id);
            $this->getApplicationTable()->saveApplication($application);
            return $this->redirect()->toRoute('applynow', ['action' => 'overview']);
        }
        return ['form' => $form];
    }

    public function sendConfirmationMailAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        $session = new \Zend\Session\Container('user');
        if ($session->role < Role::ELDER || $id === 0) {
            return $this->redirect()->toRoute('account', ['action' => 'noright']);
        }
        $application = $this->getApplicationTable()->getApplication($id);

        $mailText = 'Hello ' . $application->getName() . ' we have processed your application.';
        if ($application->getProcessed() == \ApplyNow\Model\Status::ACCEPTED) {
            $mailText .= 'We have decided for you. Please apply ingame under the clan-id: #28PU922.';
            $application->setProcessed(\ApplyNow\Model\Status::ACCEPTED_MAILED);
        } else {
            $mailText .= 'We have decided against you. We are sorry and wish you best of luck in the future.';
            $application->setProcessed(\ApplyNow\Model\Status::DECLINED_MAILED);
        }

        $this->getApplicationTable()->saveApplication($application);
        $this->appMailService->sendMail($application->getEmail(), 'Your application at Eternal Deztiny', $mailText);

        return $this->redirect()->toRoute('applynow', ['action' => 'overview']);
    }

    /**
     * @return array|ApplicationTable|object
     */
    public function getApplicationTable()
    {
        if (!$this->applicationTable) {
            $sm                     = $this->getServiceLocator();
            $this->applicationTable = $sm->get('ApplyNow\Model\ApplicationTable');
        }

        return $this->applicationTable;
    }

    public function getAccountTable()
    {
        if (!$this->accountTable) {
            $sm                 = $this->getServiceLocator();
            $this->accountTable = $sm->get('Account\Model\AccountTable');
        }

        return $this->accountTable;
    }

    private function sendApplicationMail(Application $application, \string $mail_address)
    {
        $mailText = $application->getName() . " has applied to join Eternal Destiny.\n" .
        "Ingame-Tag: #" . $application->getTag() . "\n" .
        "E-Mail adress: " . $application->getEmail() . "\n" .
        "Age: " . $application->getAge() . "\n" .
        "Town-Hall Level: " . $application->getTh() . "\n" .
        "Current war stars: " . $application->getWarStars() . "\n" .
        "About me: " . $application->getInfos() . "\n" .
        "Why I want to join ED: " . $application->getWhy() . "\n" .
        "Strategies: " . $application->getStrategies() . "\n" .
        "Process application at: " . Constants::HOST . '/applynow/detail/' . $application->getId();

        $this->appMailService->sendMail($mail_address, 'New application has arrived!', $mailText,
            [getcwd() . '/public' . $application->getBasePic(), getcwd() . '/public' . $application->getProfilePic()]);
    }
}
