<?php

namespace Warstatus\Model;

use Zend\Db\TableGateway\TableGateway;

class WarstatusTable
{
    /**
     * @var TableGateway
     */
    private $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
}
