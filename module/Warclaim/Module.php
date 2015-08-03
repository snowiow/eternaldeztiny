<?php
namespace Warclaim;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;

use Warclaim\Model\Warclaim;
use Warclaim\Model\WarclaimTable;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    public function getAutoloaderConfig()
    {
        return [
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
                'Warclaim\Model\WarclaimTable' => function ($sm) {
                    $tableGateway = $sm->get('WarclaimTableGateway');
                    $table = new WarclaimTable($tableGateway);
                    return $table;
                },
                'WarclaimTableGateway'         => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Warclaim());
                    return new TableGateway('warclaim', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

}
