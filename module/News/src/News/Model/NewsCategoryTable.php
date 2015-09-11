<?php

namespace News\Model;

use Zend\Db\TableGateway\TableGateway;

class NewsCategoryTable
{

    /**
     * @var TableGateway
     */
    protected $tableGateway;

    public function __construct()
    {
        $this->tableGateway = $tableGateway;
    }

}
