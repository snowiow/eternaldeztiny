<?php

namespace ApplyNow\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Validator\File\Size;
use Zend\View\Model\ViewModel;

use ApplyNow\Form\ApplicationForm;
use ApplyNow\Model\Application;

use AppMail\Service\AppMailServiceInterface;
use Account\Model\AccountTable;

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
                $size = new Size(['min' => 20,
                    'max'                   => 10000]);
                $adapter = new \Zend\File\Transfer\Adapter\Http();
                $adapter->setValidators([$size], $application->getProfilePic());
                $adapter->setValidators([$size], $application->getBasePic());
                if (!$adapter->isValid()) {
                    $errors = $adapter->getMessages();
                    return ['form' => $form, 'errors' => $errors];
                }
                $basePath = getcwd() . '/public/applications/' . $application->getTag() . '/';
                if (!file_exists($basePath)) {
                    mkdir($basePath);
                } else {
                    return $this->redirect()->toRoute('applynow', ['action' => 'applyfailed']);
                }

                $basePic = $application->getBasePic();
                $ending  = pathinfo($basePic['name'], PATHINFO_EXTENSION);
                $application->setBasePic($basePath . 'basepic.' . $ending);
                $basePicFile = file_get_contents($basePic['tmp_name']);
                file_put_contents($application->getBasePic(), $basePicFile);

                $profilePic = $application->getProfilePic();
                $ending     = pathinfo($profilePic['name'], PATHINFO_EXTENSION);
                $application->setProfilePic($basePath . 'profilepic.' . $ending);
                $profilePicFile = file_get_contents($profilePic['tmp_name']);
                file_put_contents($application->getProfilePic(), $profilePicFile);

                $accounts = $this->getAccountTable()->getLeadershipMails();

                foreach ($accounts as $account) {
                    $this->sendApplicationMail($application, $account->getEmail());
                }

                try {
                    $this->getApplicationTable()->saveApplication($application);
                } catch (\Exception $e) {
                    return $this->redirect()->toRoute('applynow', ['action' => 'applyfailed']);
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
        if ($session->role < 3) {
            return $this->redirect()->toRoute('account', ['action' => 'noright']);
        }
        return new ViewModel([
            'processed'   => $this->getApplicationTable()->getProcessedApplications(),
            'unprocessed' => $this->getApplicationTable()->getOpenApplications(),
        ]);
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
        "Ingame-Tag: " . $application->getTag() . "\n" .
        "E-Mail adress: " . $application->getEmail() . "\n" .
        "Age: " . $application->getAge() . "\n" .
        "Town-Hall Level: " . $application->getTh() . "\n" .
        "Current war stars: " . $application->getWarStars() . "\n" .
        "About me: " . $application->getInfos() . "\n" .
        "Why I want to join ED: " . $application->getWhy() . "\n" .
        "Strategies: " . $application->getStrategies() . "\n";
        $this->appMailService->sendMail($mail_address, 'New application has arrived!', $mailText,
            [$application->getBasePic(), $application->getProfilePic()]);
    }
}
