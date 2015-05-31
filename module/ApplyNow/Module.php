<?php

namespace ApplyNow;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

 class Module implements AutoloaderProviderInterface, ConfigProviderInterface
 {
    public function getAutoloaderConfig()
    {
        return array(
             'Zend\Loader\ClassMapAutoloader' => array(
                 __DIR__ . '/autoload_classmap.php',
             ),
             'Zend\Loader\StandardAutoloader' => array(
                 'namespaces' => array(
                     __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                 ),
             ),
         );
    }

    public function getConfig()
    {
         return include __DIR__ . '/config/module.config.php';
    }

    // public function getServiceConfig()
    // {
    //      return array(
    //          'factories' => array(
    //              'ApplyNow\Model\ApplicationTable' =>  function($sm) {
    //                  $tableGateway = $sm->get('ApplicationTableGateway');
    //                  $table = new ApplicationTable($tableGateway);
    //                  return $table;
    //              },
    //              'ApplicationTableGateway' => function ($sm) {
    //                  $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    //                  $resultSetPrototype = new ResultSet();
    //                  $resultSetPrototype->setArrayObjectPrototype(new Application());
    //                  return new TableGateway('application', $dbAdapter, null, $resultSetPrototype);
    //              },
    //          ),
    //      );
    // }
 }
