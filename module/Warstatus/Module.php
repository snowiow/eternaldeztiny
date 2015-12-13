<?php
namespace Warstatus;

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
                'Warstats\Model\WarstatsTable' => function ($sm) {
                    $tableGateway = $sm->get('WarstatsTableGateway');
                    $table        = new WarstatsTable($tableGateway);

                    return $table;
                },
                'WarstatsTableGateway'         => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Warstats());

                    return new TableGateway(
                        'warstats',
                        $dbAdapter,
                        null,
                        $resultSetPrototype
                    );
                },
            ],
        ];
    }
}
