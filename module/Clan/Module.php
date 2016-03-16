<?php
namespace Clan;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Clan\Model\Clan;
use Clan\Model\ClanTable;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
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
                'Clan\Model\ClanTable' => function ($sm) {
                    $tableGateway = $sm->get('ClanTableGateway');
                    $table        = new ClanTable($tableGateway);
                    return $table;
                },
                'ClanTableGateway'     => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Clan());
                    return new TableGateway(
                        'clan',
                        $dbAdapter,
                        null,
                        $resultSetPrototype
                    );
                },
            ],
        ];
    }
}
