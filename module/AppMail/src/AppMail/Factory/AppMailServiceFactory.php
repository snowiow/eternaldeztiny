<?php

namespace AppMail\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\ClassMethods;

use AppMail\Model\AppMail;
use AppMail\Service\AppMailService;

class AppMailServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new AppMailService($serviceLocator->get('Zend\Db\Adapter\Adapter'), new ClassMethods(false), new AppMail());
    }
}