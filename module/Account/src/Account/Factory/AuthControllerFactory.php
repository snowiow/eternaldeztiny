<?php

namespace Account\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use Account\Controller\AuthController;

class AuthControllerFactory implements FactoryInterface
{
    /**
     * Create Service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     *
     * @return AuthController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $realServiceLocator = $serviceLocator->getServiceLocator();
        $appMailService     = $realServiceLocator->get('AppMail\Service\AppMailServiceInterface');

        return new AuthController($appMailService);
    }
}
