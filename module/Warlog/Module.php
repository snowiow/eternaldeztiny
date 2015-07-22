<?php

namespace Warlog;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;

use Warlog\Model\Warlog;
use Warlog\Model\WarlogTable;

class Module
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
                'Warlog\Model\WarlogTable' => function ($sm) {
                    $tableGateway = $sm->get('WarlogTableGateway');
                    $table = new WarlogTable($tableGateway);
                    return $table;
                },
                'WarlogTableGateway'       => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Warlog());
                    return new TableGateway('warlog', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }
}
