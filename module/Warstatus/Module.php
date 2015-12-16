<?php

namespace Warstatus;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Warstatus\Model\Warstatus;
use Warstatus\Model\WarstatusTable;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

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

    public function getServiceConfig()
    {
        return [
            'factories' => [
                'Warstatus\Model\WarstatusTable' => function ($sm) {
                    $tableGateway = $sm->get('WarstatusTableGateway');
                    $table        = new WarstatusTable($tableGateway);

                    return $table;
                },
                'WarstatusTableGateway'          => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Warstatus());

                    return new TableGateway(
                        'warstatus',
                        $dbAdapter,
                        null,
                        $resultSetPrototype
                    );
                },
            ],
        ];
    }
}
