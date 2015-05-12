<?php

namespace AppMail\Model;

use Zend\Db\TableGateway\TableGateway;

class AppMailTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();

        return $resultSet;
    }

    public function getAppMail($name = 'app')
    {
        $rowset = $this->tableGateway->select(['name' => $name]);
        $row = $rowset->current();
        if (!$row) {
            return null;
        }

        return $row;
    }
}