<?php

namespace Warclaim\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Warclaim\Controller\WarclaimController;

class WarclaimControllerFactory implements FactoryInterface
{
    /**
     * Create Service
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     *
     * @return WarclaimController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $realServiceLocator = $serviceLocator->getServiceLocator();
        $appMailService     = $realServiceLocator->get('AppMail\Service\AppMailServiceInterface');

        return new WarclaimController($appMailService);
    }
}
