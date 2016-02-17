<?php

namespace Account\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Account\Controller\AccountController;

class AccountControllerFactory implements FactoryInterface
{
    /**
     * Create Service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     *
     * @return AccountController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $realServiceLocator = $serviceLocator->getServiceLocator();
        $appMailService     = $realServiceLocator->get('AppMail\Service\AppMailServiceInterface');

        return new AccountController($appMailService);
    }
}
