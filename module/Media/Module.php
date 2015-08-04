<?php
namespace Media;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Media\Model\MediaTable;
use Media\Model\Media;

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
                'Media\Model\MediaTable' => function ($sm) {
                    $tableGateway = $sm->get('MediaTableGateway');
                    $table = new MediaTable($tableGateway);
                    return $table;
                },
                'MediaTableGateway'      => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Media());
                    return new TableGateway('media', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }
}
