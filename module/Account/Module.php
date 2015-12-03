<?php

namespace Account;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;

use Account\Model\Account;
use Account\Model\AccountTable;
use News\Model\News;
use News\Model\NewsTable;
use News\Model\Comment;
use News\Model\CommentTable;
use Media\Model\Media;
use Media\Model\MediaTable;

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
                'Account\Model\AccountTable' => function ($sm) {
                    $tableGateway = $sm->get('AccountTableGateway');
                    $table        = new AccountTable($tableGateway);
                    return $table;
                },
                'AccountTableGateway'        => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Account());
                    return new TableGateway('account', $dbAdapter, null, $resultSetPrototype);
                },
                'News\Model\NewsTable'       => function ($sm) {
                    $tableGateway = $sm->get('NewsTableGateway');
                    $table        = new NewsTable($tableGateway);
                    return $table;
                },
                'NewsTableGateway'           => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new News());
                    return new TableGateway('news', $dbAdapter, null, $resultSetPrototype);
                },
                'Media\Model\MediaTable'     => function ($sm) {
                    $tableGateway = $sm->get('MediaTableGateway');
                    $table        = new MediaTable($tableGateway);
                    return $table;
                },
                'MediaTableGateway'          => function ($sm) {
                    $dbAdapter          = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Media());
                    return new TableGateway('media', $dbAdapter, null, $resultSetPrototype);
                },
                'News\Model\CommentTable'    => function ($sm) {
                    $tableGateway = $sm->get('CommentTableGateway');
                    $table        = new CommentTable($tableGateway);
                    return $table;
                },
                'CommentTableGateway'        => function ($sm) {
                    $dabAdapter         = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Comment());
                    return new TableGateway('comment', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }
}
