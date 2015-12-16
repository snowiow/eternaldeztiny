<?php

namespace ApplyNow;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

use ApplyNow\Model\Application;
use ApplyNow\Model\ApplicationTable;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\ClassMapAutoloader' => [
                __DIR__ . '/autoload_classmap.php',
            ],
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__=> __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                'ApplyNow\Model\ApplicationTable' => function ($sm) {
                    $tableGateway = $sm->get('ApplicationTableGateway');
                    $table        = new ApplicationTable($tableGateway);
                    return $table;
                },
                'ApplicationTableGateway'         => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Application());
                    return new TableGateway('application', $dbAdapter, null, $resultSetPrototype);
                },
                'Account\Model\AccountTable'      => function ($sm) {
                    $tableGateway = $sm->get('AccountTableGateway');
                    $table        = new AccountTable($tableGateway);
                    return $table;
                },
                'AccountTableGateway'             => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Account());
                    return new TableGateway('account', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }
}
