<?php

namespace Warstatus\Model;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

use Warstatus\Model\WarstatusTable;
use Warstatus\Model\Warstatus;

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

    /**
     * Retrieves an Account with the warstatus
     * @param int $account_id
     * @return null|Warstatus
     */
    public function getWarstatus(int $id)
    {
        $rowset = $this->tableGateway->select(function (Select $select)
             use ($id) {
                $select->where(['id' => $id]);
            }
        );
        $row = $rowset->current();
        return $row;
    }

    /**
     * Saves the given object ot the db
     * @param Warstatus $warstatus
     * @throws \Exception
     */
    public function saveWarstatus(Warstatus $warstatus)
    {
        $data = $warstatus->getArrayCopy();
        if ($warstatus->getId() == 0) {
            throw new \Exception('Id needed to save a warstatus');
        } else {
            if ($this->getWarstatus((int) $warstatus->getId())) {
                $this->tableGateway->update($data, ['id' => (int) $warstatus->getId()]);
            } else {
                $data['id'] = $warstatus->getId();
                $this->tableGateway->insert($data);
            }
        }
    }
}
